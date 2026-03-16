<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use ReflectionMethod;

class Vault
{
    protected string $prefix = '/';

    protected string $path = '';

    protected string $layout = 'page';

    protected Discovery $discovery = Discovery::Manual;

    /**
     * @return array<int, NavGroup>
     */
    public function navigation(): array
    {
        return [];
    }

    /**
     * Extra routable pages not in the sidebar navigation.
     *
     * @return array<int, Page>
     */
    public function pages(): array
    {
        return [];
    }

    /**
     * @return array<int, Tab>
     */
    public function tabs(): array
    {
        return [];
    }

    /**
     * @return array<int, Version>
     */
    public function versions(): array
    {
        return [];
    }

    /**
     * @return array<int, Language>
     */
    public function languages(): array
    {
        return [];
    }

    public function canAccess(Request $request): bool
    {
        return false;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getAbsolutePath(): string
    {
        $path = $this->path;

        if (! str_starts_with($path, '/')) {
            return base_path($path);
        }

        return $path;
    }

    public function getConfigPath(): string
    {
        return $this->getAbsolutePath().'/config.yml';
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function getDiscovery(): Discovery
    {
        return $this->discovery;
    }

    public function hasNavigation(): bool
    {
        if ($this->isMethodOverridden('navigation')) {
            return true;
        }

        return $this->navigation() !== [];
    }

    public function hasPages(): bool
    {
        if ($this->isMethodOverridden('pages')) {
            return true;
        }

        return $this->pages() !== [];
    }

    public function hasTabs(): bool
    {
        if ($this->isMethodOverridden('tabs')) {
            return true;
        }

        return $this->tabs() !== [];
    }

    public function hasVersions(): bool
    {
        if ($this->isMethodOverridden('versions')) {
            return true;
        }

        return $this->versions() !== [];
    }

    public function hasLanguages(): bool
    {
        if ($this->isMethodOverridden('languages')) {
            return true;
        }

        return $this->languages() !== [];
    }

    /**
     * Resolve the navigation groups for the given context.
     *
     * Resolution order (ADR §2.10):
     * 1. Auto discovery? → derive from filesystem
     * 2. Has versions? → find version, check if it has its own tabs
     * 3. Has tabs? → find tab → use tab's groups
     * 4. Fall back to navigation()
     *
     * @return array<int, NavGroup>
     */
    public function resolveNavigation(
        ?string $language = null,
        ?string $version = null,
        ?string $tab = null,
    ): array {
        if ($this->discovery === Discovery::Auto) {
            $scanner = new FilesystemScanner($this->getScanBasePath($language, $version));

            return $scanner->scanNavigation();
        }

        $tabs = $this->resolveActiveTabs($version);

        if ($tabs !== [] && $tab !== null) {
            foreach ($tabs as $tabObj) {
                if ($tabObj->key === $tab) {
                    return $tabObj->getGroups();
                }
            }

            return $tabs[0]->getGroups();
        }

        if ($tabs !== []) {
            return $tabs[0]->getGroups();
        }

        return $this->navigation();
    }

    /**
     * Get all resolved pages as a flat map (slug => Page) for the given context.
     *
     * @return array<string, Page>
     */
    public function resolvePages(
        ?string $language = null,
        ?string $version = null,
        ?string $tab = null,
    ): array {
        $groups = $this->resolveNavigation($language, $version, $tab);
        $pages = [];

        foreach ($groups as $group) {
            foreach ($group->getPages() as $page) {
                $pages[$page->getSlug()] = $page;
            }
        }

        foreach ($this->pages() as $page) {
            if (! isset($pages[$page->getSlug()])) {
                $pages[$page->getSlug()] = $page;
            }
        }

        if ($this->discovery !== Discovery::Manual) {
            $scanner = new FilesystemScanner($this->getScanBasePath($language, $version));

            foreach ($scanner->scanPages() as $page) {
                if (! isset($pages[$page->getSlug()])) {
                    $pages[$page->getSlug()] = $page;
                }
            }
        }

        return $pages;
    }

    /**
     * Resolve the file path for a page given the hierarchy context.
     */
    public function resolveFilePath(
        Page $page,
        ?string $language = null,
        ?string $version = null,
    ): string {
        $basePath = rtrim($this->getAbsolutePath(), '/');

        if ($language !== null) {
            $basePath .= '/'.$language;
        }

        if ($version !== null) {
            $basePath .= '/'.$version;
        }

        return $basePath.'/'.$page->getPath();
    }

    /**
     * Collect all page slugs across all possible tabs/versions.
     *
     * @return array<int, string>
     */
    public function collectAllSlugs(): array
    {
        return match ($this->discovery) {
            Discovery::Manual => $this->collectManualSlugs(),
            Discovery::Mapped, Discovery::Auto => $this->collectDiscoveredSlugs(),
        };
    }

    /**
     * Collect all tab keys across vault and all versions.
     *
     * @return array<int, string>
     */
    public function collectAllTabKeys(): array
    {
        $keys = [];

        foreach ($this->tabs() as $tab) {
            $keys[] = $tab->key;
        }

        foreach ($this->versions() as $version) {
            if ($version->hasTabs()) {
                foreach ($version->getTabs() ?? [] as $tab) {
                    $keys[] = $tab->key;
                }
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * Get the default language, or null if no languages defined.
     */
    public function getDefaultLanguage(): ?Language
    {
        foreach ($this->languages() as $language) {
            if ($language->isDefault()) {
                return $language;
            }
        }

        $languages = $this->languages();

        return $languages !== [] ? $languages[0] : null;
    }

    /**
     * Get the default version, or null if no versions defined.
     */
    public function getDefaultVersion(): ?Version
    {
        foreach ($this->versions() as $version) {
            if ($version->isDefault()) {
                return $version;
            }
        }

        $versions = $this->versions();

        return $versions !== [] ? $versions[0] : null;
    }

    /**
     * Build the base path for filesystem scanning, including language/version segments.
     */
    private function getScanBasePath(?string $language = null, ?string $version = null): string
    {
        $basePath = rtrim($this->getAbsolutePath(), '/');

        if ($language !== null) {
            $basePath .= '/'.$language;
        }

        if ($version !== null) {
            $basePath .= '/'.$version;
        }

        return $basePath;
    }

    /**
     * Collect slugs from navigation(), tabs(), versions(), and pages().
     *
     * @return array<int, string>
     */
    private function collectManualSlugs(): array
    {
        $slugs = [];

        foreach ($this->navigation() as $group) {
            foreach ($group->getPages() as $page) {
                $slugs[] = $page->getSlug();
            }
        }

        foreach ($this->tabs() as $tab) {
            foreach ($tab->getGroups() as $group) {
                foreach ($group->getPages() as $page) {
                    $slugs[] = $page->getSlug();
                }
            }
        }

        foreach ($this->versions() as $version) {
            if ($version->hasTabs()) {
                foreach ($version->getTabs() ?? [] as $tab) {
                    foreach ($tab->getGroups() as $group) {
                        foreach ($group->getPages() as $page) {
                            $slugs[] = $page->getSlug();
                        }
                    }
                }
            }
        }

        foreach ($this->pages() as $page) {
            $slugs[] = $page->getSlug();
        }

        return array_values(array_unique($slugs));
    }

    /**
     * Collect slugs from filesystem scanning + pages() + navigation() (for Mapped).
     *
     * @return array<int, string>
     */
    private function collectDiscoveredSlugs(): array
    {
        $scanner = new FilesystemScanner($this->getAbsolutePath());
        $slugs = $scanner->scanSlugs();

        foreach ($this->pages() as $page) {
            $slugs[] = $page->getSlug();
        }

        if ($this->discovery === Discovery::Mapped) {
            foreach ($this->navigation() as $group) {
                foreach ($group->getPages() as $page) {
                    $slugs[] = $page->getSlug();
                }
            }

            foreach ($this->tabs() as $tab) {
                foreach ($tab->getGroups() as $group) {
                    foreach ($group->getPages() as $page) {
                        $slugs[] = $page->getSlug();
                    }
                }
            }

            foreach ($this->versions() as $version) {
                if ($version->hasTabs()) {
                    foreach ($version->getTabs() ?? [] as $tab) {
                        foreach ($tab->getGroups() as $group) {
                            foreach ($group->getPages() as $page) {
                                $slugs[] = $page->getSlug();
                            }
                        }
                    }
                }
            }
        }

        return array_values(array_unique($slugs));
    }

    /**
     * @return array<int, Tab>
     */
    private function resolveActiveTabs(?string $version): array
    {
        if ($version !== null && $this->hasVersions()) {
            foreach ($this->versions() as $versionObj) {
                if ($versionObj->key === $version && $versionObj->hasTabs()) {
                    return $versionObj->getTabs() ?? [];
                }
            }
        }

        return $this->tabs();
    }

    private function isMethodOverridden(string $method): bool
    {
        $reflection = new ReflectionMethod($this, $method);

        return $reflection->getDeclaringClass()->getName() !== self::class;
    }
}
