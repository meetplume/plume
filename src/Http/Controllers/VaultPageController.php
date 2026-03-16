<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Plume;
use Meetplume\Plume\ThemeConfig;
use Meetplume\Plume\Vault;

class VaultPageController
{
    public function __invoke(Request $request): Response
    {
        /** @var string $vaultPrefix */
        $vaultPrefix = $request->route()->defaults['vaultPrefix'];

        $plume = app(Plume::class);
        $config = $plume->getConfiguration();

        abort_unless($config !== null, 404);

        $vault = $config->getVault($vaultPrefix);

        abort_unless($vault !== null, 404);
        abort_unless($vault->canAccess($request), 403);

        $language = $request->route('language');
        $version = $request->route('version');
        $tab = $request->route('tab');
        $slug = $request->route('slug');

        if ($language === null && $vault->hasLanguages()) {
            $language = $vault->getDefaultLanguage()?->code;
        }

        if ($version === null && $vault->hasVersions()) {
            $version = $vault->getDefaultVersion()?->key;
        }

        if ($tab === null && ($vault->hasTabs() || ($version !== null && $this->versionHasTabs($vault, $version)))) {
            $tabs = $this->resolveActiveTabs($vault, $version);

            if ($tabs !== []) {
                $tab = $tabs[0]->key;
            }
        }

        $pages = $vault->resolvePages($language, $version, $tab);

        /** @var Page|null $page */
        $page = $pages[$slug] ?? null;

        abort_unless($page !== null, 404);

        $filePath = $vault->resolveFilePath($page, $language, $version);
        $pageProps = $page->toInertiaProps($filePath);

        $navigation = $vault->resolveNavigation($language, $version, $tab);
        $navigationArray = $this->buildNavigationArray($navigation, $vault, $page->getSlug(), $language, $version, $tab);

        $component = 'plume/'.$vault->getLayout();

        $props = [
            ...$pageProps,
            'layout' => $vault->getLayout(),
            'vault' => [
                'prefix' => $vault->getPrefix(),
            ],
            'navigation' => $navigationArray,
        ];

        $prefix = trim($vault->getPrefix(), '/');
        $pageDir = dirname($page->getPath());
        $assetBase = '/'.$prefix.'/_content';

        if ($pageDir !== '.') {
            $assetBase .= '/'.trim($pageDir, '/');
        }

        $props['contentAssetBase'] = $assetBase;

        if (! empty($pageProps['meta']['sections'])) {
            $props['sections'] = $pageProps['meta']['sections'];
        }

        $header = $config->getHeader();
        if ($header !== null) {
            $headerArray = $header->toArray();
            $headerArray['collectionTitle'] = $config->getName();
            $headerArray['logo'] = $config->getLogo();
            $headerArray['logoDark'] = $config->getLogoDark();
            $props['header'] = $headerArray;
        }

        $footer = $config->getFooter();
        if ($footer !== null) {
            $props['footer'] = $footer->toArray();
        }

        $props['site'] = [
            'name' => $config->getName(),
            'logo' => $config->getLogo(),
            'logoDark' => $config->getLogoDark(),
        ];

        if ($vault->hasTabs() || $this->versionHasTabs($vault, $version)) {
            $activeTabs = $this->resolveActiveTabs($vault, $version);
            $props['tabs'] = $this->buildTabsArray($activeTabs, $vault, $tab, $language, $version);
            $props['activeTab'] = $tab;
        }

        if ($vault->hasVersions()) {
            $props['versions'] = $this->buildVersionsArray($vault, $version, $language, $tab, $slug);
            $props['activeVersion'] = $version;
        }

        if ($vault->hasLanguages()) {
            $props['languages'] = $this->buildLanguagesArray($vault, $language, $version, $tab, $slug);
            $props['activeLanguage'] = $language;
        }

        $prevNext = $this->buildPrevNext($navigation, $vault, $page->getSlug(), $language, $version, $tab);
        $props['prev'] = $prevNext['prev'];
        $props['next'] = $prevNext['next'];

        $this->shareThemeData($vault);

        Inertia::setRootView('plume::app');

        return Inertia::render($component, $props);
    }

    /**
     * @param  array<int, NavGroup>  $groups
     * @return list<array<string, mixed>>
     */
    private function buildNavigationArray(
        array $groups,
        Vault $vault,
        string $currentSlug,
        ?string $language,
        ?string $version,
        ?string $tab,
    ): array {
        $items = [];
        $prefix = trim($vault->getPrefix(), '/');

        foreach ($groups as $group) {
            $pages = [];

            foreach ($group->getPages() as $page) {
                if ($page->isHidden()) {
                    continue;
                }

                $pages[] = [
                    'type' => 'page',
                    'key' => $page->key,
                    'label' => $page->resolveLabel($vault->resolveFilePath($page, $language, $version)),
                    'slug' => $page->getSlug(),
                    'href' => $this->buildPageHref($prefix, $page->getSlug(), $language, $version, $tab),
                    'hidden' => $page->isHidden(),
                    'active' => $currentSlug === $page->getSlug(),
                ];
            }

            $items[] = [
                'type' => 'group',
                'key' => $group->key,
                'label' => $group->getLabel(),
                'icon' => $group->getIcon(),
                'pages' => $pages,
            ];
        }

        return $items;
    }

    /**
     * @param  array<int, \Meetplume\Plume\Tab>  $tabs
     * @return list<array{key: string, label: string, icon: ?string, href: string, active: bool}>
     */
    private function buildTabsArray(
        array $tabs,
        Vault $vault,
        ?string $activeTab,
        ?string $language,
        ?string $version,
    ): array {
        $prefix = trim($vault->getPrefix(), '/');
        $result = [];

        foreach ($tabs as $tabObj) {
            $firstSlug = null;
            foreach ($tabObj->getGroups() as $group) {
                $pages = $group->getPages();

                if ($pages !== []) {
                    $firstSlug = $pages[0]->getSlug();
                    break;
                }
            }

            $result[] = [
                'key' => $tabObj->key,
                'label' => $tabObj->getLabel(),
                'icon' => $tabObj->getIcon(),
                'href' => $this->buildPageHref($prefix, $firstSlug ?? '', $language, $version, $tabObj->key),
                'active' => $activeTab === $tabObj->key,
            ];
        }

        return $result;
    }

    /**
     * @return list<array{key: string, href: string, active: bool, default: bool}>
     */
    private function buildVersionsArray(
        Vault $vault,
        ?string $activeVersion,
        ?string $language,
        ?string $tab,
        ?string $slug,
    ): array {
        $prefix = trim($vault->getPrefix(), '/');
        $result = [];

        foreach ($vault->versions() as $versionObj) {
            $result[] = [
                'key' => $versionObj->key,
                'href' => $this->buildPageHref($prefix, $slug ?? '', $language, $versionObj->key, $tab),
                'active' => $activeVersion === $versionObj->key,
                'default' => $versionObj->isDefault(),
            ];
        }

        return $result;
    }

    /**
     * @return list<array{code: string, name: string, href: string, active: bool}>
     */
    private function buildLanguagesArray(
        Vault $vault,
        ?string $activeLanguage,
        ?string $version,
        ?string $tab,
        ?string $slug,
    ): array {
        $prefix = trim($vault->getPrefix(), '/');
        $result = [];

        foreach ($vault->languages() as $lang) {
            $result[] = [
                'code' => $lang->code,
                'name' => $lang->name,
                'href' => $this->buildPageHref($prefix, $slug ?? '', $lang->code, $version, $tab),
                'active' => $activeLanguage === $lang->code,
            ];
        }

        return $result;
    }

    /**
     * @param  array<int, NavGroup>  $groups
     * @return array{prev: ?array{label: string, href: string}, next: ?array{label: string, href: string}}
     */
    private function buildPrevNext(
        array $groups,
        Vault $vault,
        string $currentSlug,
        ?string $language,
        ?string $version,
        ?string $tab,
    ): array {
        $prefix = trim($vault->getPrefix(), '/');
        $allPages = [];

        foreach ($groups as $group) {
            foreach ($group->getPages() as $page) {
                if (! $page->isHidden()) {
                    $allPages[] = $page;
                }
            }
        }

        $currentIndex = array_find_key($allPages, fn (Page $page): bool => $page->getSlug() === $currentSlug);

        if ($currentIndex === null) {
            return ['prev' => null, 'next' => null];
        }

        $prev = null;
        $next = null;

        if ($currentIndex > 0) {
            $prevPage = $allPages[$currentIndex - 1];
            $prev = [
                'label' => $prevPage->resolveLabel($vault->resolveFilePath($prevPage, $language, $version)),
                'href' => $this->buildPageHref($prefix, $prevPage->getSlug(), $language, $version, $tab),
            ];
        }

        if ($currentIndex < count($allPages) - 1) {
            $nextPage = $allPages[$currentIndex + 1];
            $next = [
                'label' => $nextPage->resolveLabel($vault->resolveFilePath($nextPage, $language, $version)),
                'href' => $this->buildPageHref($prefix, $nextPage->getSlug(), $language, $version, $tab),
            ];
        }

        return ['prev' => $prev, 'next' => $next];
    }

    private function buildPageHref(
        string $prefix,
        string $slug,
        ?string $language,
        ?string $version,
        ?string $tab,
    ): string {
        $segments = [$prefix];

        if ($language !== null) {
            $segments[] = $language;
        }

        if ($version !== null) {
            $segments[] = $version;
        }

        if ($tab !== null) {
            $segments[] = $tab;
        }

        $segments[] = $slug;

        return '/'.implode('/', $segments);
    }

    private function shareThemeData(Vault $vault): void
    {
        $themeConfig = $this->resolveVaultThemeConfig($vault);

        if (! $themeConfig instanceof \Meetplume\Plume\ThemeConfig) {
            return;
        }

        app()->instance(ThemeConfig::class, $themeConfig);

        $themeArray = $themeConfig->toArray();
        $plumeData = [
            'theme' => $themeArray,
            'codeThemeLight' => $themeArray['code_theme_light'],
            'codeThemeDark' => $themeArray['code_theme_dark'],
        ];
        if (app()->environment('local') && $themeConfig->isCustomizerEnabled()) {
            $plumeData['customizer'] = [
                'enabled' => true,
                'preset' => $themeConfig->activePreset(),
                'presets' => ThemeConfig::presets(),
                'vault' => trim($vault->getPrefix(), '/'),
            ];
        }

        Inertia::share('plume', $plumeData);
    }

    private function resolveVaultThemeConfig(Vault $vault): ?ThemeConfig
    {
        $vaultConfigPath = $vault->getConfigPath();

        if (file_exists($vaultConfigPath)) {
            return new ThemeConfig($vaultConfigPath);
        }

        if (app()->bound(ThemeConfig::class)) {
            return app(ThemeConfig::class);
        }

        return null;
    }

    /**
     * @return array<int, \Meetplume\Plume\Tab>
     */
    private function resolveActiveTabs(Vault $vault, ?string $version): array
    {
        if ($version !== null) {
            foreach ($vault->versions() as $versionObj) {
                if ($versionObj->key === $version && $versionObj->hasTabs()) {
                    return $versionObj->getTabs() ?? [];
                }
            }
        }

        return $vault->tabs();
    }

    private function versionHasTabs(Vault $vault, ?string $version): bool
    {
        if ($version === null) {
            return false;
        }

        foreach ($vault->versions() as $versionObj) {
            if ($versionObj->key === $version) {
                return $versionObj->hasTabs();
            }
        }

        return false;
    }
}
