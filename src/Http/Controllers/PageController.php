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
        /** @var string|null $collectionPrefix */
        $collectionPrefix = $request->route()->defaults['collectionPrefix'] ?? null;

        /** @var ?Collection $collection */
        $collection = $collectionPrefix !== null
            ? app(Plume::class)->getCollection($collectionPrefix)
            : null;

        /** @var string $slug */
        $slug = $request->route('slug');

        /** @var PageItem $pageItem */
        $pageItem = $collection
            ? $collection->getPage($slug)
            : $request->route()->defaults['pageItem'];

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
        }

        return Page::render('plume/page', $props, $collection);
    }
}
