<?php

namespace App\View;

use App\Services\ThemeService;
use Illuminate\View\FileViewFinder;

class ThemeViewFinder extends FileViewFinder
{
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

        // Copy hints from the original finder
        $this->hints = $originalFinder->getHints();
    }

    /**
     * Find the given view in the list of paths.
     */
    public function find($name): string
    {
        // Try the theme view first
        $themeView = $this->themeService->getViewPath($name);
        if ($themeView && file_exists($themeView)) {
            return $themeView;
        }

        // Fallback to the original finder
        return $this->originalFinder->find($name);
    }

    /**
     * Get an array of possible view files.
     */
    public function getPossibleViewFiles($name): array
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
    public function addLocation($location): void
    {
        $this->originalFinder->addLocation($location);
        parent::addLocation($location);
    }

    /**
     * Add a namespace hint to the finder.
     */
    public function addNamespace($namespace, $hints): void
    {
        $this->originalFinder->addNamespace($namespace, $hints);
        parent::addNamespace($namespace, $hints);
    }

    /**
     * Prepend a namespace hint to the finder.
     */
    public function prependNamespace($namespace, $hints): void
    {
        $this->originalFinder->prependNamespace($namespace, $hints);
        parent::prependNamespace($namespace, $hints);
    }

    /**
     * Replace the namespace hints for the given namespace.
     */
    public function replaceNamespace($namespace, $hints): void
    {
        $this->originalFinder->replaceNamespace($namespace, $hints);
        parent::replaceNamespace($namespace, $hints);
    }

    /**
     * Add a valid view extension to the finder.
     */
    public function addExtension($extension): void
    {
        $this->originalFinder->addExtension($extension);
        parent::addExtension($extension);
    }

    /**
     * Flush the cache of located views.
     */
    public function flush(): void
    {
        $this->originalFinder->flush();
        parent::flush();
    }
}
