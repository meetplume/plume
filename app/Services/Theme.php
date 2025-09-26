<?php

namespace App\Services;

class Theme
{
    protected ThemeService $themeService;
    protected ThemeFieldsService $themeFieldsService;
    protected ?string $themeName;

    public function __construct(ThemeService $themeService, ThemeFieldsService $themeFieldsService, ?string $themeName = null)
    {
        $this->themeService = $themeService;
        $this->themeFieldsService = $themeFieldsService;
        $this->themeName = $themeName;
    }

    /**
     * Get theme fields fluent interface
     */
    public function fields(?string $setting = null): ThemeFields
    {
        return new ThemeFields($this->themeFieldsService, $setting, $this->themeName);
    }

    /**
     * Get the active theme name
     */
    public function active(): string
    {
        return $this->themeName ?? $this->themeService->getActiveTheme();
    }

    /**
     * Get theme configuration
     */
    public function config(): array
    {
        return $this->themeService->getThemeConfig();
    }

    public function settings(): ?array
    {
        return $this->config()['settings'] ?? null;
    }

    /**
     * Check if the theme exists
     */
    public function exists(string $theme): bool
    {
        return $this->themeService->themeExists($theme);
    }

    /**
     * Activate a theme
     */
    public function activate(string $theme): bool
    {
        return $this->themeService->activateTheme($theme);
    }
}
