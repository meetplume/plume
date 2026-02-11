<?php

namespace Meetplume\Plume;

use Inertia\Inertia;
use Inertia\Response;

class Page
{
    public static function render(string $component, array $props = []): Response
    {
        Inertia::setRootView('plume::app');

        return Inertia::render($component, $props);
    }

    public static function toMarkdown(string $path): Response
    {
        return static::render('markdown', ['content' => file_get_contents($path)]);
    }
}
