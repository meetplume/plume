<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Meetplume\Plume\Page;
use Meetplume\Plume\PageDefinition;

class PageController
{
    public function __invoke(Request $request): Response
    {
        /** @var PageDefinition $definition */
        $definition = $request->route()->defaults['pageDefinition'];

        abort_unless(file_exists($definition->filePath), 404);

        return Page::render('plume/page', $definition->toInertiaProps());
    }
}
