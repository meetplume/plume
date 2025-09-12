<?php

use App\Services\Theme;

if (!function_exists('theme')) {
    /**
     * Get the theme fluent API instance
     */
    function theme(?string $themeName = null): Theme
    {
        return app(Theme::class, ['themeName' => $themeName]);
    }
}
