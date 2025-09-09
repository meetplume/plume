<?php

namespace App\Support\Nav;

use App\Enums\MainPages;
use App\Enums\SiteSettings;
use App\Models\Page;

class DropdownMenu
{
    /**
     * Process dropdown menu items and return structured data for rendering.
     */
    public static function getDropdownMenuItems(): array
    {
        $dropdownMenuItems = SiteSettings::MAIN_MENU_MORE->get() ?? [];
        $processedItems = [];

        foreach ($dropdownMenuItems as $dropdownItem) {
            $processedItems[] = self::processDropdownMenuItem($dropdownItem);
        }

        return $processedItems;
    }

    /**
     * Process a single dropdown menu item.
     */
    private static function processDropdownMenuItem(array $dropdownItem): array
    {
        $type = data_get($dropdownItem, 'type');

        if ($type === 'divider') {
            return [
                'type' => 'divider',
                'label' => data_get($dropdownItem, 'data.label'),
            ];
        }

        $defaultLanguage = SiteSettings::DEFAULT_LANGUAGE->get();
        $currentLocale = app()->getLocale();
        $localePrefix = ($currentLocale !== $defaultLanguage) ? "/{$currentLocale}" : '';

        $pageKey = data_get($dropdownItem, 'data.page');
        $url = url($localePrefix); // safe default with locale
        $name = '';

        if (filled($pageKey) && $pageKey !== 'custom') {
            if (str_starts_with($pageKey, 'page:')) {
                [$url, $name] = self::processPageDropdownItem($pageKey);
            } else {
                [$url, $name] = self::processEnumPageDropdownItem($pageKey, $localePrefix);
            }
        } else {
            [$url, $name] = self::processCustomDropdownItem($dropdownItem, $localePrefix);
        }

        return [
            'type' => 'item',
            'name' => $name,
            'url' => $url,
            'icon' => data_get($dropdownItem, 'data.icon'),
            'open_in_new_tab' => data_get($dropdownItem, 'data.open_in_new_tab', false),
        ];
    }

    /**
     * Process page-specific dropdown items (format: page:ID).
     */
    private static function processPageDropdownItem(string $pageKey): array
    {
        $pageId = (int) str($pageKey)->after('page:')->toString();
        $pageModel = Page::find($pageId);

        if ($pageModel) {
            return [
                route('pages.show', ['page' => $pageModel]),
                $pageModel->title,
            ];
        }

        return [url('/'), 'Page'];
    }

    /**
     * Process enum-based page dropdown items.
     */
    private static function processEnumPageDropdownItem(string $pageKey, string $localePrefix): array
    {
        $path = data_get(SiteSettings::PERMALINKS->get(), $pageKey);
        $url = url($localePrefix.'/'.$path);
        $name = MainPages::tryFrom($pageKey)->getTitle();

        return [$url, $name];
    }

    /**
     * Process custom dropdown items with direct URLs.
     */
    private static function processCustomDropdownItem(array $dropdownItem, string $localePrefix): array
    {
        $path = data_get($dropdownItem, 'data.url');
        $name = data_get($dropdownItem, 'data.name');

        // Don't add locale prefix to external URLs or anchor links
        if (self::isExternalOrSpecialUrl($path)) {
            $url = url($path);
        } else {
            $url = url($localePrefix.$path);
        }

        return [$url, $name];
    }

    /**
     * Check if URL is external, anchor, or mailto.
     */
    private static function isExternalOrSpecialUrl(string $url): bool
    {
        return str_starts_with($url, 'http') ||
               str_starts_with($url, '#') ||
               str_starts_with($url, 'mailto:');
    }
}
