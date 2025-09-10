<?php

namespace App\Providers;

use App\Services\ThemeService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
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

            return new class($finder, $themeService) extends FileViewFinder {
                protected FileViewFinder $originalFinder;
                protected ThemeService $themeService;

                public function __construct(FileViewFinder $originalFinder, ThemeService $themeService)
                {
                    $this->originalFinder = $originalFinder;
                    $this->themeService = $themeService;

                    // Initialize parent with original finder's properties
                    parent::__construct(
                        $originalFinder->getFilesystem(),
                        $originalFinder->getPaths(),
                        $originalFinder->getExtensions()
                    );

                    // Copy hints from original finder
                    $this->hints = $originalFinder->getHints();
                }

                /**
                 * Find the given view in the list of paths.
                 */
                public function find($name)
                {
                    // Try theme view first
                    $themeView = $this->themeService->getViewPath($name);
                    if ($themeView && file_exists($themeView)) {
                        return $themeView;
                    }

                    // Fallback to original finder
                    return $this->originalFinder->find($name);
                }

                /**
                 * Get the path to a template with a named path.
                 */
                public function findNamespaceView($name)
                {
                    return $this->originalFinder->findNamespaceView($name);
                }

                /**
                 * Get an array of possible view files.
                 */
                public function getPossibleViewFiles($name)
                {
                    $files = [];

                    // Add theme view possibilities first
                    $themeView = $this->themeService->getViewPath($name);
                    if ($themeView) {
                        $files[] = $themeView;
                    }

                    // Add original possibilities
                    $originalFiles = $this->originalFinder->getPossibleViewFiles($name);
                    $files = array_merge($files, $originalFiles);

                    return array_unique($files);
                }

                /**
                 * Add a location to the finder.
                 */
                public function addLocation($location)
                {
                    $this->originalFinder->addLocation($location);
                    parent::addLocation($location);
                }

                /**
                 * Add a namespace hint to the finder.
                 */
                public function addNamespace($namespace, $hints)
                {
                    $this->originalFinder->addNamespace($namespace, $hints);
                    parent::addNamespace($namespace, $hints);
                }

                /**
                 * Prepend a namespace hint to the finder.
                 */
                public function prependNamespace($namespace, $hints)
                {
                    $this->originalFinder->prependNamespace($namespace, $hints);
                    parent::prependNamespace($namespace, $hints);
                }

                /**
                 * Replace the namespace hints for the given namespace.
                 */
                public function replaceNamespace($namespace, $hints)
                {
                    $this->originalFinder->replaceNamespace($namespace, $hints);
                    parent::replaceNamespace($namespace, $hints);
                }

                /**
                 * Add a valid view extension to the finder.
                 */
                public function addExtension($extension)
                {
                    $this->originalFinder->addExtension($extension);
                    parent::addExtension($extension);
                }

                /**
                 * Flush the cache of located views.
                 */
                public function flush()
                {
                    $this->originalFinder->flush();
                    parent::flush();
                }

                /**
                 * Delegate other method calls to original finder
                 */
                public function __call($method, $arguments)
                {
                    if (method_exists($this->originalFinder, $method)) {
                        return $this->originalFinder->$method(...$arguments);
                    }

                    return parent::__call($method, $arguments);
                }
            };
        });
    }

    /**
     * Publish theme assets if they don't exist
     */
    protected function publishThemeAssets(): void
    {
        $themeService = $this->app->make(ThemeService::class);
        $themes = $themeService->getAvailableThemes();

        foreach (array_keys($themes) as $themeName) {
            $sourcePath = resource_path("themes/{$themeName}/assets");
            $targetPath = public_path("themes/{$themeName}");

            // Only publish if source exists and target doesn't exist or is outdated
            if (is_dir($sourcePath) && (!is_dir($targetPath) || $this->assetsNeedUpdate($sourcePath, $targetPath))) {
                try {
                    if (!is_dir($targetPath)) {
                        File::makeDirectory($targetPath, 0755, true);
                    }
                    File::copyDirectory($sourcePath, $targetPath);
                } catch (\Exception $e) {
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
        if (!is_dir($targetPath)) {
            return true;
        }

        // Simple check: if source is newer than target
        $sourceTime = filemtime($sourcePath);
        $targetTime = filemtime($targetPath);

        return $sourceTime > $targetTime;
    }
}
