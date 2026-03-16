<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use ReflectionMethod;

class Vault
{
    protected string $prefix = '/';

    protected string $path = '';

    protected string $layout = 'page';

    /**
     * @return array<int, NavGroup>
     */
    public function navigation(): array
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

    public function hasNavigation(): bool
    {
        if ($this->isMethodOverridden('navigation')) {
            return true;
        }

        return $this->navigation() !== [];
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
     * 1. Has versions? → find version, check if it has its own tabs
     * 2. Has tabs? → find tab → use tab's groups
     * 3. Fall back to navigation()
     *
     * @return array<int, NavGroup>
     */
    public function resolveNavigation(
        ?string $language = null,
        ?string $version = null,
        ?string $tab = null,
    ): array {
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
        $slugs = [];

        $allGroups = $this->navigation();
        foreach ($allGroups as $group) {
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

        return array_values(array_unique($slugs));
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
