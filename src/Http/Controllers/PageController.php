<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Meetplume\Plume\Collection;
use Meetplume\Plume\Page;
use Meetplume\Plume\PageItem;
use Meetplume\Plume\Plume;
use Meetplume\Plume\ThemeConfig;

class PageController
{
    public function __invoke(Request $request): Response
    {
        /** @var PageItem $pageItem */
        $pageItem = $request->route()->defaults['pageItem'];

        /** @var ?Collection $collection */
        $collection = $request->route()->defaults['collection'] ?? null;

        if ($collection !== null && $collection->getConfigPath() !== null) {
            $globalConfigPath = app(Plume::class)->configPath();
            $themeConfig = new ThemeConfig($collection->getConfigPath(), $globalConfigPath);
            app()->instance(ThemeConfig::class, $themeConfig);
        }

        $filePath = $collection
            ? $collection->resolveFilePath($pageItem)
            : $pageItem->getFilePath();

        $props = $pageItem->toInertiaProps($filePath);

        if ($collection) {
            $props['codeThemeLight'] ??= $collection->getCodeThemeLight()?->value;
            $props['codeThemeDark'] ??= $collection->getCodeThemeDark()?->value;
            $props['collection'] = [
                'title' => $collection->getTitle(),
                'description' => $collection->getDescription(),
            ];
            $props['navigation'] = $collection->toNavigationArray($pageItem->getSlug());

            $prefix = trim($collection->prefix, '/');
            $pageDir = dirname($pageItem->getPath());
            $assetBase = '/'.$prefix.'/_content';

            if ($pageDir !== '.') {
                $assetBase .= '/'.trim($pageDir, '/');
            }

            $props['contentAssetBase'] = $assetBase;

            // Pass blocks data from frontmatter and collection
            if (! empty($props['meta']['sections'])) {
                $props['sections'] = $props['meta']['sections'];
            }

            if ($collection->getHeader() !== null) {
                $props['header'] = $collection->getHeader();
            }

            if ($collection->getFooter() !== null) {
                $props['footer'] = $collection->getFooter();
            }
        }

        return Page::render('plume/page', $props, $collection);
    }
}
