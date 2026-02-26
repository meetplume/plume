<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Meetplume\Plume\Collection;
use Meetplume\Plume\Page;
use Meetplume\Plume\PageItem;

class PageController
{
    public function __invoke(Request $request): Response
    {
        /** @var PageItem $pageItem */
        $pageItem = $request->route()->defaults['pageItem'];

        /** @var ?Collection $collection */
        $collection = $request->route()->defaults['collection'] ?? null;

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
        }

        return Page::render('plume/page', $props);
    }
}
