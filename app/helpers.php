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

if (!function_exists('wrapPhikiCode')) {
    function wrapPhikiCode(string $html): string
    {
        // Pattern to match Phiki-generated code blocks
        $pattern = '/<pre[^>]*class="[^"]*phiki[^"]*"[^>]*>.*?<\/pre>/s';

        return preg_replace_callback($pattern, function ($matches) {
            $codeBlock = $matches[ 0 ];

            return sprintf(
                '<div class="phiki-wrapper">%s</div>',
                $codeBlock
            );
        }, $html);
    }
}
