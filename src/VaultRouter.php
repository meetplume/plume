<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Http\Controllers\ContentAssetController;
use Meetplume\Plume\Http\Controllers\VaultPageController;

final class VaultRouter
{
    public function register(Vault $vault): void
    {
        $prefix = trim($vault->getPrefix(), '/');
        $hasLanguages = $vault->hasLanguages();
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

        $this->registerContentAssetRoute($prefix);

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
    }

    private function registerContentAssetRoute(string $prefix): void
    {
        Route::get(sprintf('%s/_content/{path}', $prefix), ContentAssetController::class)
            ->where('path', '.*')
            ->defaults('vaultPrefix', $prefix)
            ->name(sprintf('plume.%s._content', $prefix));
    }

    private function anyVersionHasTabs(Vault $vault): bool
    {
        return array_any($vault->versions(), fn (Version $version): bool => $version->hasTabs());
    }
}
