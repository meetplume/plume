<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Meetplume\Plume\Collection;
use Meetplume\Plume\Frontmatter;
use Meetplume\Plume\Page;
use Meetplume\Plume\PageItem;

class CollectionPageController
{
    public function __invoke(Request $request): Response
    {
        /** @var Collection $collection */
        $collection = $request->route()->defaults['collection'];

        /** @var PageItem $pageItem */
        $pageItem = $request->route()->defaults['pageItem'];

        $filePath = $collection->resolveFilePath($pageItem);

        abort_unless(file_exists($filePath), 404);

        $rawContent = (string) file_get_contents($filePath);
        $frontmatter = Frontmatter::parse($rawContent);

        return Page::render('plume/page', [
            'content' => $rawContent,
            'title' => $frontmatter['title'] ?? null,
            'description' => $frontmatter['description'] ?? null,
            'meta' => $frontmatter,
            'collection' => [
                'title' => $collection->getTitle(),
                'description' => $collection->getDescription(),
            ],
            'navigation' => $collection->toNavigationArray($pageItem->getSlug()),
        ]);
    }
}
