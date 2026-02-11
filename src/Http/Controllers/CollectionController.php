<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Routing\Controller;
use Meetplume\Plume\CollectionRegistry;
use Meetplume\Plume\Page;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    public function __construct(
        private CollectionRegistry $registry,
    ) {}

    public function index(string $prefix): Response
    {
        $collection = $this->registry->resolve($prefix);

        if (! $collection) {
            abort(404);
        }

        $type = $collection->getType();
        $props = $type->indexProps($collection);

        return Page::render($type->indexComponent(), $props)->toResponse(request());
    }

    public function show(string $slug, string $prefix): Response
    {
        $collection = $this->registry->resolve($prefix);

        if (! $collection) {
            abort(404);
        }

        $file = $collection->findFileBySlug($slug);

        if (! $file) {
            abort(404);
        }

        $type = $collection->getType();
        $props = $type->showProps($collection, $slug);

        return Page::render($type->showComponent(), $props)->toResponse(request());
    }
}
