<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Http\Controllers\PageController;

class Plume
{
    public function config(string $configPath): void
    {
        $themeConfig = new ThemeConfig($configPath);
        app()->instance(ThemeConfig::class, $themeConfig);
    }

    public function page(string $uri, string $filePath): PageDefinition
    {
        $definition = new PageDefinition($filePath);
        $trimmedUri = trim($uri, '/');

        Route::get($uri, PageController::class)
            ->defaults('pageDefinition', $definition)
            ->name("plume.{$trimmedUri}");

        return $definition;
    }
}
