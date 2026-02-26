<?php

namespace Meetplume\Plume;

use Inertia\Inertia;
use Inertia\Response;

class Page
{
    public static function make(string $key): PageItem
    {
        return new PageItem($key);
    }

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
            $themeArray = $themeConfig->toArray();

            $plumeData = [
                'theme' => $themeArray,
                'codeThemeLight' => $themeArray['code_theme_light'],
                'codeThemeDark' => $themeArray['code_theme_dark'],
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
