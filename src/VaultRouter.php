<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Http\Controllers\ContentAssetController;
use Meetplume\Plume\Http\Controllers\DiagnosticsController;
use Meetplume\Plume\Http\Controllers\SearchIndexController;
use Meetplume\Plume\Http\Controllers\VaultPageController;

final class VaultRouter
{
    public function register(Vault $vault): void
    {
        $prefix = trim($vault->getPrefix(), '/');
        // When the app picks the language, the URL carries none.
        $hasLanguages = $vault->hasLanguages() && ! $vault->hasAppLanguage();
        $hasVersions = $vault->hasVersions();
        $hasTabs = $vault->hasTabs();
        $vault->hasNavigation();

        $allSlugs = $vault->collectAllSlugs();

        if ($allSlugs === []) {
            return;
        }

        $slugConstraint = implode('|', array_map(fn (string $s): string => preg_quote($s, '/'), $allSlugs));

        $languageCodes = $hasLanguages
            ? array_map(fn (Language $l): string => preg_quote($l->code, '/'), $vault->languages())
            : [];

        $versionKeys = $hasVersions
            ? array_map(fn (Version $v): string => preg_quote($v->key, '/'), $vault->versions())
            : [];

        $tabKeys = ($hasTabs || $this->anyVersionHasTabs($vault))
            ? array_map(fn (string $k): string => preg_quote($k, '/'), $vault->collectAllTabKeys())
            : [];

        $this->registerAbsoluteRoutes($vault, $prefix);
        $this->registerDiagnosticsRoute($prefix);
        $this->registerContentAssetRoute($prefix);
        $this->registerSearchIndexRoute($prefix);

        $rootSlug = array_find($allSlugs, fn (string $s): bool => $s === '/');

        if ($rootSlug !== null) {
            $allSlugs = array_values(array_filter($allSlugs, fn (string $s): bool => $s !== '/'));
            $slugConstraint = implode('|', array_map(fn (string $s): string => preg_quote($s, '/'), $allSlugs));
            $this->registerRootRoute($prefix);

            if ($languageCodes !== []) {
                $this->registerLanguageRootRoute($prefix, $languageCodes);
            }
        }

        if ($allSlugs === []) {
            return;
        }

        $this->registerFullRoute($prefix, $slugConstraint, $languageCodes, $versionKeys, $tabKeys);

        if ($hasLanguages) {
            $this->registerWithoutLanguageRoute($prefix, $slugConstraint, $versionKeys, $tabKeys);
        }

        if ($hasVersions) {
            $this->registerWithoutVersionRoute($prefix, $slugConstraint, $languageCodes, $tabKeys);
        }

        if ($hasLanguages && $hasVersions) {
            $this->registerWithoutLanguageAndVersionRoute($prefix, $slugConstraint, $tabKeys);
        }
    }

    private function registerFullRoute(
        string $prefix,
        string $slugConstraint,
        /** @var array<int, string> */ array $languageCodes,
        /** @var array<int, string> */ array $versionKeys,
        /** @var array<int, string> */ array $tabKeys,
    ): void {
        $segments = [$prefix];
        $constraints = ['slug' => $slugConstraint];

        if ($languageCodes !== []) {
            $segments[] = '{language}';
            $constraints['language'] = implode('|', $languageCodes);
        }

        if ($versionKeys !== []) {
            $segments[] = '{version}';
            $constraints['version'] = implode('|', $versionKeys);
        }

        if ($tabKeys !== []) {
            $segments[] = '{tab}';
            $constraints['tab'] = implode('|', $tabKeys);
        }

        $segments[] = '{slug}';

        $pattern = implode('/', $segments);

        $route = Route::get($pattern, VaultPageController::class)
            ->defaults('vaultPrefix', $prefix)
            ->name('plume.'.$prefix);

        foreach ($constraints as $param => $constraint) {
            $route->where($param, $constraint);
        }

        $this->registerMarkdownRoute($pattern, 'plume.'.$prefix, $prefix, $constraints);
    }

    /**
     * Route without language segment (uses default language).
     */
    private function registerWithoutLanguageRoute(
        string $prefix,
        string $slugConstraint,
        /** @var array<int, string> */ array $versionKeys,
        /** @var array<int, string> */ array $tabKeys,
    ): void {
        $segments = [$prefix];
        $constraints = ['slug' => $slugConstraint];

        if ($versionKeys !== []) {
            $segments[] = '{version}';
            $constraints['version'] = implode('|', $versionKeys);
        }

        if ($tabKeys !== []) {
            $segments[] = '{tab}';
            $constraints['tab'] = implode('|', $tabKeys);
        }

        $segments[] = '{slug}';

        $route = Route::get(implode('/', $segments), VaultPageController::class)
            ->defaults('vaultPrefix', $prefix)
            ->name('plume.'.$prefix.'.no-lang');

        foreach ($constraints as $param => $constraint) {
            $route->where($param, $constraint);
        }

        $this->registerMarkdownRoute(implode('/', $segments), 'plume.'.$prefix.'.no-lang', $prefix, $constraints);
    }

    /**
     * Route without version segment (uses default version).
     */
    private function registerWithoutVersionRoute(
        string $prefix,
        string $slugConstraint,
        /** @var array<int, string> */ array $languageCodes,
        /** @var array<int, string> */ array $tabKeys,
    ): void {
        $segments = [$prefix];
        $constraints = ['slug' => $slugConstraint];

        if ($languageCodes !== []) {
            $segments[] = '{language}';
            $constraints['language'] = implode('|', $languageCodes);
        }

        if ($tabKeys !== []) {
            $segments[] = '{tab}';
            $constraints['tab'] = implode('|', $tabKeys);
        }

        $segments[] = '{slug}';

        $route = Route::get(implode('/', $segments), VaultPageController::class)
            ->defaults('vaultPrefix', $prefix)
            ->name('plume.'.$prefix.'.no-ver');

        foreach ($constraints as $param => $constraint) {
            $route->where($param, $constraint);
        }

        $this->registerMarkdownRoute(implode('/', $segments), 'plume.'.$prefix.'.no-ver', $prefix, $constraints);
    }

    /**
     * Route without language and version segments (uses defaults for both).
     */
    private function registerWithoutLanguageAndVersionRoute(
        string $prefix,
        string $slugConstraint,
        /** @var array<int, string> */ array $tabKeys,
    ): void {
        $segments = [$prefix];
        $constraints = ['slug' => $slugConstraint];

        if ($tabKeys !== []) {
            $segments[] = '{tab}';
            $constraints['tab'] = implode('|', $tabKeys);
        }

        $segments[] = '{slug}';

        $route = Route::get(implode('/', $segments), VaultPageController::class)
            ->defaults('vaultPrefix', $prefix)
            ->name('plume.'.$prefix.'.no-lang-ver');

        foreach ($constraints as $param => $constraint) {
            $route->where($param, $constraint);
        }

        $this->registerMarkdownRoute(implode('/', $segments), 'plume.'.$prefix.'.no-lang-ver', $prefix, $constraints);
    }

    private function registerRootRoute(string $prefix): void
    {
        Route::get($prefix, VaultPageController::class)
            ->defaults('vaultPrefix', $prefix)
            ->defaults('slug', '/')
            ->name('plume.'.$prefix.'.root');

        $this->registerMarkdownRoute($prefix, 'plume.'.$prefix.'.root', $prefix, defaults: ['slug' => '/']);
    }

    /**
     * The home of each language, e.g. `/docs/pt` (the bare prefix serves the default one).
     *
     * @param  array<int, string>  $languageCodes
     */
    private function registerLanguageRootRoute(string $prefix, array $languageCodes): void
    {
        Route::get($prefix.'/{language}', VaultPageController::class)
            ->where('language', implode('|', $languageCodes))
            ->defaults('vaultPrefix', $prefix)
            ->defaults('slug', '/')
            ->name('plume.'.$prefix.'.root.lang');

        $this->registerMarkdownRoute($prefix.'/{language}', 'plume.'.$prefix.'.root.lang', $prefix, ['language' => implode('|', $languageCodes)], ['slug' => '/']);
    }

    private function registerAbsoluteRoutes(Vault $vault, string $vaultPrefix): void
    {
        foreach ($vault->pages() as $page) {
            if ($page->getRoute() === null) {
                continue;
            }

            $routePath = trim($page->getRoute(), '/');

            Route::get($routePath, VaultPageController::class)
                ->defaults('vaultPrefix', $vaultPrefix)
                ->defaults('slug', $page->getSlug())
                ->name('plume.'.$vaultPrefix.'.route.'.$page->key);

            $this->registerMarkdownRoute($routePath, 'plume.'.$vaultPrefix.'.route.'.$page->key, $vaultPrefix, defaults: ['slug' => $page->getSlug()]);
        }
    }

    /**
     * The address of a page's Markdown: the page's own, followed by `.md`.
     */
    public static function markdownUri(string $uri): string
    {
        $uri = trim($uri, '/');

        return $uri === '' ? 'index.md' : $uri.'.md';
    }

    /**
     * The page's Markdown, through the page's own controller: the same
     * middleware, canAccess() and page lookup guard it.
     *
     * @param  array<string, string>  $constraints
     * @param  array<string, string>  $defaults
     */
    private function registerMarkdownRoute(string $uri, string $name, string $vaultPrefix, array $constraints = [], array $defaults = []): void
    {
        $route = Route::get(self::markdownUri($uri), VaultPageController::class)
            ->defaults('vaultPrefix', $vaultPrefix)
            ->defaults('markdown', true)
            ->name($name.'.markdown');

        foreach ($defaults as $key => $value) {
            $route->defaults($key, $value);
        }

        foreach ($constraints as $param => $constraint) {
            $route->where($param, $constraint);
        }
    }

    private function registerDiagnosticsRoute(string $prefix): void
    {
        if (! app()->isLocal() && ! app()->runningUnitTests()) {
            return;
        }

        Route::get(sprintf('%s/_plume', $prefix), DiagnosticsController::class)
            ->defaults('vaultPrefix', $prefix)
            ->name(sprintf('plume.%s._plume', $prefix));
    }

    private function registerContentAssetRoute(string $prefix): void
    {
        Route::get(sprintf('%s/_content/{path}', $prefix), ContentAssetController::class)
            ->where('path', '.*')
            ->defaults('vaultPrefix', $prefix)
            ->name(sprintf('plume.%s._content', $prefix));
    }

    private function registerSearchIndexRoute(string $prefix): void
    {
        Route::get(sprintf('%s/_plume/search-index.json', $prefix), SearchIndexController::class)
            ->defaults('vaultPrefix', $prefix)
            ->name(sprintf('plume.%s._plume.search', $prefix));
    }

    private function anyVersionHasTabs(Vault $vault): bool
    {
        return array_any($vault->versions(), fn (Version $version): bool => $version->hasTabs());
    }
}
