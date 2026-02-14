<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Enums\CodeTheme;
use Meetplume\Plume\Http\Controllers\PageController;

class Plume
{
    public function page(
        string $uri,
        string $filePath,
        CodeTheme $codeThemeLight = CodeTheme::GITHUB_LIGHT,
        CodeTheme $codeThemeDark = CodeTheme::GITHUB_DARK,
    ): void {
        $trimmedUri = trim($uri, '/');

        Route::get($uri, PageController::class)
            ->defaults('filePath', $filePath)
            ->defaults('codeThemeLight', $codeThemeLight)
            ->defaults('codeThemeDark', $codeThemeDark)
            ->name("plume.{$trimmedUri}");
    }
}
