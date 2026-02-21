<?php

namespace Meetplume\Plume;

use Inertia\Inertia;
use Inertia\Response;

class Page
{
    /**
     * @param  array<string, mixed>|string  $props  Array of props or path to a frontmatter file.
     */
    public static function render(string $component, array|string $props = []): Response
    {
        if (is_string($props)) {
            abort_unless(file_exists($props), 404);

            $props = Frontmatter::parse((string) file_get_contents($props));
        }

        if (app()->bound(ThemeConfig::class)) {
            $themeConfig = app(ThemeConfig::class);

            $plumeData = [
                'theme' => $themeConfig->toArray(),
            ];

            if (app()->environment('local') && $themeConfig->isCustomizerEnabled()) {
                $plumeData['customizer'] = [
                    'enabled' => true,
                    'preset' => $themeConfig->activePreset(),
                    'presets' => ThemeConfig::presets(),
                ];
            }

            Inertia::share('plume', $plumeData);
        }

        Inertia::setRootView('plume::app');

        return Inertia::render($component, $props);
    }
}
