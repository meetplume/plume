<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Http\Controllers\PageController;

class Plume
{
    private ?string $configPath = null;

    /** @var array<string, Collection> */
    private array $collections = [];

    public function collection(string $prefix, string $contentPath): Collection
    {
        $collection = new Collection($prefix, $contentPath);
        $this->collections[trim($prefix, '/')] = $collection;

        return $collection;
    }

    public function config(string $configPath): void
    {
        $this->configPath = $configPath;
        $themeConfig = new ThemeConfig($configPath);
        app()->instance(ThemeConfig::class, $themeConfig);
    }

    public function configPath(): ?string
    {
        return $this->configPath;
    }

    public function page(string $uri, string $filePath): PageItem
    {
        $pageItem = new PageItem(trim($uri, '/'))->filePath($filePath);
        $trimmedUri = trim($uri, '/');

        Route::get($uri, PageController::class)
            ->defaults('pageItem', $pageItem)
            ->name('plume.'.$trimmedUri);

        return $pageItem;
    }
}
