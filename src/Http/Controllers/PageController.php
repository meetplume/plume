<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Meetplume\Plume\Enums\CodeTheme;
use Meetplume\Plume\Frontmatter;
use Meetplume\Plume\Page;

class PageController
{
    public function __invoke(Request $request): Response
    {
        $filePath = $request->route()->defaults['filePath'];
        /** @var CodeTheme $codeThemeLight */
        $codeThemeLight = $request->route()->defaults['codeThemeLight'];
        /** @var CodeTheme $codeThemeDark */
        $codeThemeDark = $request->route()->defaults['codeThemeDark'];

        abort_unless(file_exists($filePath), 404);

        $rawContent = file_get_contents($filePath);
        $frontmatter = Frontmatter::parse($rawContent);

        return Page::render('plume/page', [
            'content' => $rawContent,
            'title' => $frontmatter['title'] ?? null,
            'description' => $frontmatter['description'] ?? null,
            'meta' => $frontmatter,
            'codeThemeLight' => $codeThemeLight->value,
            'codeThemeDark' => $codeThemeDark->value,
        ]);
    }
}
