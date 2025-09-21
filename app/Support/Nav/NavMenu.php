<?php

namespace App\Support\Nav;

use App\Enums\MainPages;
use App\Enums\SiteSettings;
use App\Models\Page;
use Illuminate\Support\Str;

class NavMenu
{
    /**
     * Process main menu items and return structured data for rendering.
     *
     * @return NavMenuItem[]
     */
    public static function getMainMenuItems(): array
    {
        $mainMenuItems = SiteSettings::MAIN_MENU->get() ?? [];
        $processedItems = [];

        foreach ($mainMenuItems as $menuItem) {
            if (data_get($menuItem, 'page') === 'user-account') {
                $processedItems[] = NavMenuItem::makeComponent(
                    component: 'nav.account-dropdown',
                    nameForAnalytics: 'account'
                );
            } else {
                $processedItems[] = self::processMainMenuItem($menuItem);
            }
        }

        return $processedItems;
    }

    /**
     * Process a single main menu item.
     */
    private static function processMainMenuItem(array $menuItem): NavMenuItem
    {
        $pageKey = data_get($menuItem, 'page');
        $icon = data_get($menuItem, 'icon');
        $openInNewTab = data_get($menuItem, 'open_in_new_tab', false);

        if (filled($pageKey) && $pageKey !== 'custom') {
            if (str_starts_with($pageKey, 'page:')) {
                [$url, $name, $nameForAnalytics] = self::processPageMenuItem($pageKey);
            } else {
                [$url, $name, $nameForAnalytics] = self::processEnumPageMenuItem($pageKey);
            }
        } else {
            [$url, $name, $nameForAnalytics] = self::processCustomMenuItem($menuItem);
        }

        $isActive = self::isMenuItemActive($url);

        return NavMenuItem::make(
            $name,
            $url,
            $nameForAnalytics,
            $icon,
            $openInNewTab,
            $isActive
        );
    }

    /**
     * Process page-specific menu items (format: page:ID).
     */
    private static function processPageMenuItem(string $pageKey): array
    {
        $pageId = (int) str($pageKey)->after('page:')->toString();
        $pageModel = Page::query()->find($pageId);

        if ($pageModel) {
            return [
                route('pages.show', ['page' => $pageModel]),
                $pageModel->title,
                $pageModel->getTranslation('title', SiteSettings::DEFAULT_LANGUAGE->get()),
            ];
        }

        return [url('/'), 'Page', 'Page'];
    }

    /**
     * Process enum-based page menu items.
     */
    private static function processEnumPageMenuItem(string $pageKey): array
    {
        $permalink = data_get(SiteSettings::PERMALINKS->get(), $pageKey);
        $url = filled($permalink) ? url($permalink) : url('/');
        $name = MainPages::tryFrom($pageKey)?->getTitle() ?? '';
        $nameForAnalytics = MainPages::tryFrom($pageKey)?->value ?? '';

        return [$url, $name, $nameForAnalytics];
    }

    /**
     * Process custom menu items with direct URLs.
     */
    private static function processCustomMenuItem(array $menuItem): array
    {
        $raw = data_get($menuItem, 'url');
        $url = url('/'); // safe default

        if (is_string($raw) && self::isExternalOrSpecialUrl($raw)) {
            $url = $raw;
        } else {
            $url = filled($raw) ? url($raw) : url('/');
        }

        $name = data_get($menuItem, 'name');
        $nameForAnalytics = $name;

        return [$url, $name, $nameForAnalytics];
    }

    /**
     * Check if URL is external, anchor, or mailto.
     */
    private static function isExternalOrSpecialUrl(string $url): bool
    {
        return Str::startsWith($url, ['http', '#', 'mailto:']);
    }

    /**
     * Check if a menu item is currently active.
     */
    private static function isMenuItemActive(string $url): bool
    {
        $currentUrl = str(request()->url())->remove('/'.app()->getLocale())->toString();

        return $currentUrl === $url;
    }
}
