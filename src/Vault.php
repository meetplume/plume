<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Enums\PageAction;
use ReflectionMethod;

class Vault
{
    protected string $prefix = '/';

    protected string $path = '';

    protected string $layout = 'page';

    protected ?string $home = null;

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

    /**
     * Let the app pick the language of each request instead of the URL — a
     * per-tenant setting, a user preference. Return one of `languages()`:
     * Plume then serves that language with no language switcher and no
     * language segment in routes or links, and fills in the pages it lacks
     * from the default language. Null, or an unknown code, means the default.
     */
    public function languageFor(Request $request): ?string
    {
        return null;
    }

    /**
     * A link back to the app the vault lives in, at the bottom of the sidebar,
     * e.g. `['label' => __('Back to the app'), 'href' => '/']`.
     *
     * @return array{label: string, href: string}|null
     */
    public function backLink(Request $request): ?array
    {
        return null;
    }

    /**
     * Whether the app, through `languageFor()`, picks the language rather than the URL.
     */
    public function hasAppLanguage(): bool
    {
        return $this->hasLanguages() && $this->isMethodOverridden('languageFor');
    }

    /**
     * The language `languageFor()` picks for the request, among the vault's languages.
     */
    public function appLanguageFor(Request $request): ?string
    {
        $code = $this->languageFor($request);

        foreach ($this->languages() as $language) {
            if ($language->code === $code) {
                return $code;
            }
        }

        return $this->getDefaultLanguage()?->code;
    }

    /**
     * The entries of the page menu, next to the page title. Return `[]` to
     * leave the menu out, or `PageAction::internal()` for a vault that ChatGPT
     * and Claude cannot read, behind a login for instance.
     *
     * @return array<int, PageAction>
     */
    public function pageActions(): array
    {
        return PageAction::cases();
    }

    /**
     * Whether the reader of the request sees a page, from its key and its
     * frontmatter (say, the feature or the permission it documents). A page
     * the reader does not see is left out of the sidebar, the search results,
     * the previous/next links and the tabs, and answers 404.
     *
     * @param  array<string, mixed>  $frontmatter
     */
    public function canSeePage(Request $request, string $key, array $frontmatter): bool
    {
        return true;
    }

    /**
     * Whether the vault filters its pages per reader, through `canSeePage()`.
     */
    public function hasPageVisibility(): bool
    {
        return $this->isMethodOverridden('canSeePage');
    }

    public function pageIsVisible(Request $request, Page $page, ?string $language = null, ?string $version = null): bool
    {
        if (! $this->hasPageVisibility()) {
            return true;
        }

        return $this->canSeePage($request, $page->key, $this->frontmatterOf($page, $language, $version));
    }

    /**
     * The navigation as the reader of the request sees it.
     *
     * @return array<int, NavGroup>
     */
    public function visibleNavigation(Request $request, ?string $language = null, ?string $version = null, ?string $tab = null): array
    {
        $groups = $this->resolveNavigation($language, $version, $tab);

        if (! $this->hasPageVisibility()) {
            return $groups;
        }

        return array_map(
            fn (NavGroup $group): NavGroup => (clone $group)->pages(array_values(array_filter(
                $group->getPages(),
                fn (Page $page): bool => $this->pageIsVisible($request, $page, $language, $version),
            ))),
            $groups,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function frontmatterOf(Page $page, ?string $language = null, ?string $version = null): array
    {
        $path = $this->resolveFilePath($page, $language, $version);

        return is_file($path) ? Frontmatter::parse((string) file_get_contents($path)) : [];
    }

    /**
     * Route middleware of the vault: its pages, search index and content assets.
     *
     * Keep `web` for the session, and add what the host needs before
     * `canAccess()` runs — `auth` to send guests to the login page, or a
     * middleware that keeps the vault off some hosts.
     *
     * @return array<int, string>
     */
    public function middleware(): array
    {
        return ['web'];
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
        $path = $this->getPath();

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

    public function getHome(): string
    {
        return $this->home ?? '/'.trim($this->prefix, '/');
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
        return $this->localizeGroups($this->resolveSourceNavigation($language, $version, $tab), $language);
    }

    /**
     * The navigation as declared or discovered, before translated slugs.
     *
     * @return array<int, NavGroup>
     */
    private function resolveSourceNavigation(?string $language, ?string $version, ?string $tab): array
    {
        if ($this->discovery === Discovery::Auto) {
            $scanner = $this->scanner($language, $version);

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

        $slugs = $this->translatedSlugs($language);

        foreach ($this->pages() as $page) {
            $page = $this->localizePage($page, $slugs);
            $pages[$page->getSlug()] ??= $page;
        }

        if ($this->discovery !== Discovery::Manual) {
            $scanner = $this->scanner($language, $version);

            foreach ($scanner->scanPages() as $page) {
                $page = $this->localizePage($page, $slugs);
                $pages[$page->getSlug()] ??= $page;
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
        return $this->getScanBasePath($page->getLanguage() ?? $language, $version).'/'.$page->getPath();
    }

    /**
     * Collect all page slugs across all possible tabs/versions.
     *
     * @return array<int, string>
     */
    public function collectAllSlugs(): array
    {
        $slugs = match ($this->discovery) {
            Discovery::Manual => $this->collectManualSlugs(),
            Discovery::Mapped, Discovery::Auto => $this->collectDiscoveredSlugs(),
        };

        foreach ($this->languages() as $language) {
            array_push($slugs, ...array_values($language->getSlugs()));
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
     * The scanner of a language folder. When the app picks the language, the
     * default language fills in the pages that folder lacks.
     */
    private function scanner(?string $language, ?string $version): FilesystemScanner
    {
        $default = $this->getDefaultLanguage()?->code;

        if (! $this->hasAppLanguage() || $language === null || $language === $default) {
            return new FilesystemScanner($this->getScanBasePath($language, $version));
        }

        return new FilesystemScanner($this->getScanBasePath($language, $version), $this->getScanBasePath($default, $version), $default);
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
     * Gives each page the slug its language translates it to (`Language::slugs()`).
     *
     * @param  array<int, NavGroup>  $groups
     * @return array<int, NavGroup>
     */
    private function localizeGroups(array $groups, ?string $language): array
    {
        $slugs = $this->translatedSlugs($language);

        if ($slugs === []) {
            return $groups;
        }

        return array_map(
            fn (NavGroup $group): NavGroup => (clone $group)->pages(
                array_map(fn (Page $page): Page => $this->localizePage($page, $slugs), $group->getPages()),
            ),
            $groups,
        );
    }

    /**
     * @param  array<string, string>  $slugs  page key => translated slug
     */
    private function localizePage(Page $page, array $slugs): Page
    {
        if (! isset($slugs[$page->key])) {
            return $page;
        }

        return (clone $page)->slug($slugs[$page->key]);
    }

    /**
     * @return array<string, string>
     */
    private function translatedSlugs(?string $language): array
    {
        foreach ($this->languages() as $candidate) {
            if ($candidate->code === $language) {
                return $candidate->getSlugs();
            }
        }

        return [];
    }

    /**
     * Every content folder the vault reads from: one per language and version
     * combination, since each lives in its own `{language}/{version}` subfolder.
     *
     * @return array<int, string>
     */
    private function scanBasePaths(): array
    {
        $languages = array_map(fn (Language $language): string => $language->code, $this->languages()) ?: [null];
        $versions = array_map(fn (Version $version): string => $version->key, $this->versions()) ?: [null];
        $paths = [];

        foreach ($languages as $language) {
            foreach ($versions as $version) {
                $paths[] = $this->getScanBasePath($language, $version);
            }
        }

        return $paths;
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
            if ($page->getRoute() === null) {
                $slugs[] = $page->getSlug();
            }
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
        $slugs = [];

        foreach ($this->scanBasePaths() as $basePath) {
            array_push($slugs, ...new FilesystemScanner($basePath)->scanSlugs());
        }

        foreach ($this->pages() as $page) {
            if ($page->getRoute() === null) {
                $slugs[] = $page->getSlug();
            }
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
