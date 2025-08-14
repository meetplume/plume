<?php

namespace App\Support\Footer;

use App\Models\Page;
use App\Enums\MainPages;
use App\Enums\SiteSettings;

class FooterMenu
{
    /**
     * Process footer menu items and return structured data for rendering.
     *
     * @return FooterMenuItem[]
     */
    public static function getFooterMenuItems(): array
    {
        $footerMenuItems = SiteSettings::FOOTER_MENU->get() ?? [];
        $processedItems = [];

        foreach ($footerMenuItems as $footerMenuItem) {
            $processedItems[] = self::processFooterMenuItem($footerMenuItem);
        }

        return $processedItems;
    }

    /**
     * Process a single footer menu item.
     */
    private static function processFooterMenuItem(array $footerMenuItem): FooterMenuItem
    {
        $pageKey = data_get($footerMenuItem, 'page');
        $url = url('/'); // safe default
        $name = '';

        if (filled($pageKey) && $pageKey !== 'custom') {
            if (str_starts_with($pageKey, 'page:')) {
                [$url, $name] = self::processPageMenuItem($pageKey);
            } else {
                [$url, $name] = self::processEnumPageMenuItem($pageKey);
            }
        } else {
            [$url, $name] = self::processCustomMenuItem($footerMenuItem);
        }

        return FooterMenuItem::make(
            $name,
            $url,
            data_get($footerMenuItem, 'open_in_new_tab', false)
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
                $pageModel->title
            ];
        }

        return [url('/'), 'Page'];
    }

    /**
     * Process enum-based page menu items.
     */
    private static function processEnumPageMenuItem(string $pageKey): array
    {
        $permalink = data_get(SiteSettings::PERMALINKS->get(), $pageKey);
        $url = filled($permalink) ? url($permalink) : url('/');
        $name = MainPages::tryFrom($pageKey)?->getTitle() ?? 'Page';

        return [$url, $name];
    }

    /**
     * Process custom menu items with direct URLs.
     */
    private static function processCustomMenuItem(array $footerMenuItem): array
    {
        $raw = data_get($footerMenuItem, 'url');
        $url = url('/'); // safe default

        if (is_string($raw) && self::isExternalOrSpecialUrl($raw)) {
            // External, anchor, or mailto: keep as-is
            $url = $raw;
        } else {
            $url = filled($raw) ? url($raw) : url('/');
        }

        $name = data_get($footerMenuItem, 'name');

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
