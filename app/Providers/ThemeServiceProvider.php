<?php

namespace App\Providers;

use Exception;
use App\Services\ThemeService;
use App\View\ThemeViewFinder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ThemeService::class);
    }

    /**
     * Bootstrap services.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        $this->registerThemeViewFinder();
        $this->publishThemeAssets();
    }

    /**
     * Register custom view finder that checks theme views first
     */
    protected function registerThemeViewFinder(): void
    {
        $this->app->extend('view.finder', function (FileViewFinder $finder, $app) {
            $themeService = $app->make(ThemeService::class);

            return new ThemeViewFinder($finder, $themeService);
        });
    }

    /**
     * Publish theme assets if they don't exist
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function publishThemeAssets(): void
    {
        $themeService = $this->app->make(ThemeService::class);
        $themes = $themeService->getAvailableThemes();

        foreach (array_keys($themes) as $themeName) {
            $sourcePath = resource_path("themes/$themeName/assets");
            $targetPath = public_path("themes/$themeName");

            // Only publish if the source exists and the target doesn't exist or is outdated
            if (is_dir($sourcePath) && (! is_dir($targetPath) || $this->assetsNeedUpdate($sourcePath, $targetPath))) {
                try {
                    if (! is_dir($targetPath)) {
                        File::makeDirectory($targetPath, 0755, true);
                    }
                    File::copyDirectory($sourcePath, $targetPath);
                } catch (Exception) {
                    // Silently fail - assets can be published manually via artisan command
                }
            }
        }
    }

    /**
     * Check if theme assets need to be updated
     */
    protected function assetsNeedUpdate(string $sourcePath, string $targetPath): bool
    {
        if (! is_dir($targetPath)) {
            return true;
        }

        return filemtime($sourcePath) > filemtime($targetPath);
    }
}
