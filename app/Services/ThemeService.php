<?php

namespace App\Services;

use App\Enums\SiteSettings;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ThemeService
{
    protected string $activeTheme;
    protected string $themePath;

    public function __construct()
    {
        $this->activeTheme = SiteSettings::ACTIVE_THEME->get() ?? 'default';
        $this->themePath = resource_path("themes/{$this->activeTheme}");
    }

    /**
     * Get the active theme name
     */
    public function getActiveTheme(): string
    {
        return $this->activeTheme;
    }

    /**
     * Get the path to a theme view if it exists
     */
    public function getViewPath(string $view): ?string
    {
        // Convert dot notation to path
        $viewPath = str_replace('.', '/', $view);
        $themeView = "{$this->themePath}/views/{$viewPath}.blade.php";

        if (file_exists($themeView)) {
            return $themeView;
        }

        return null; // Falls back to default Laravel view resolution
    }

    /**
     * Get the theme configuration from theme.json
     */
    public function getThemeConfig(): array
    {
        $configPath = "{$this->themePath}/theme.json";

        if (!file_exists($configPath)) {
            return [];
        }

        $content = file_get_contents($configPath);
        return json_decode($content, true) ?? [];
    }

    /**
     * Get URL for theme asset
     */
    public function getThemeAssetUrl(string $asset): string
    {
        return asset("themes/{$this->activeTheme}/assets/{$asset}");
    }

    /**
     * Get absolute path to theme asset
     */
    public function getThemeAssetPath(string $asset): string
    {
        return public_path("themes/{$this->activeTheme}/assets/{$asset}");
    }

    /**
     * Apply theme settings to SiteSettings
     */
    public function applyThemeSettings(): void
    {
        $config = $this->getThemeConfig();

        if (!isset($config['settings'])) {
            return;
        }

        foreach ($config['settings'] as $key => $value) {
            if (is_array($value)) {
                continue; // Skip complex settings for now
            }

            try {
                $setting = SiteSettings::from($key);
                $setting->set($value);
            } catch (\ValueError) {
                // Setting doesn't exist, skip
            }
        }
    }

    /**
     * Get all available themes
     */
    public function getAvailableThemes(): array
    {
        $themes = [];
        $themesPath = resource_path('themes');

        if (!is_dir($themesPath)) {
            return [];
        }

        foreach (glob("{$themesPath}/*", GLOB_ONLYDIR) as $themeDir) {
            $themeName = basename($themeDir);
            $configPath = "{$themeDir}/theme.json";

            if (file_exists($configPath)) {
                $config = json_decode(file_get_contents($configPath), true);
                if ($config) {
                    $themes[$themeName] = $config;
                }
            } else {
                // Theme without config file - add basic info
                $themes[$themeName] = [
                    'name' => ucfirst($themeName),
                    'description' => 'Theme without configuration',
                    'version' => '1.0.0',
                ];
            }
        }

        return $themes;
    }

    /**
     * Check if a theme exists
     */
    public function themeExists(string $theme): bool
    {
        return is_dir(resource_path("themes/{$theme}"));
    }

    /**
     * Activate a theme
     */
    public function activateTheme(string $theme): bool
    {
        if (!$this->themeExists($theme)) {
            return false;
        }

        SiteSettings::ACTIVE_THEME->set($theme);
        $this->activeTheme = $theme;
        $this->themePath = resource_path("themes/{$theme}");

        // Apply theme settings if available
        $this->applyThemeSettings();

        // Apply theme custom field defaults if available
        $this->applyThemeFieldDefaults($theme);

        // Publish theme assets
        $this->publishThemeAssets($theme);

        // Clear cache
        cache()->forget("primary_palette_generated");

        return true;
    }

    /**
     * Apply theme field default values when activating a theme
     */
    protected function applyThemeFieldDefaults(string $theme): void
    {
        $themeFieldsService = app(\App\Services\ThemeFieldsService::class);
        $fields = $themeFieldsService->getThemeFields($theme);

        foreach ($fields as $field) {
            $fieldKey = $field['key'] ?? '';
            $defaultValue = $field['default'] ?? null;

            if ($fieldKey && $defaultValue !== null) {
                $settingKey = "theme_{$theme}_{$fieldKey}";

                // Only set default if no value exists yet
                $existingValue = \Rawilk\Settings\Facades\Settings::get($settingKey);
                if ($existingValue === null) {
                    \Rawilk\Settings\Facades\Settings::set($settingKey, $defaultValue);
                }
            }
        }
    }

    /**
     * Create theme directory structure
     */
    public function createTheme(string $name, ?string $copyFrom = null): bool
    {
        $themePath = resource_path("themes/{$name}");

        if (is_dir($themePath)) {
            return false; // Theme already exists
        }

        // Create theme directory
        File::makeDirectory($themePath, 0755, true);

        if ($copyFrom && $this->themeExists($copyFrom)) {
            // Copy from existing theme
            $sourcePath = resource_path("themes/{$copyFrom}");
            File::copyDirectory($sourcePath, $themePath);

            // Update theme.json with new name
            $configPath = "{$themePath}/theme.json";
            if (file_exists($configPath)) {
                $config = json_decode(file_get_contents($configPath), true);
                $config['name'] = ucfirst($name) . ' Theme';
                file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));
            }
        } else {
            // Create basic theme structure
            $this->createBasicThemeStructure($themePath, $name);
        }

        return true;
    }

    /**
     * Create basic theme structure
     */
    private function createBasicThemeStructure(string $themePath, string $name): void
    {
        // Create directories
        File::makeDirectory("{$themePath}/views/components", 0755, true);
        File::makeDirectory("{$themePath}/views/posts", 0755, true);
        File::makeDirectory("{$themePath}/views/pages", 0755, true);
        File::makeDirectory("{$themePath}/partials", 0755, true);
        File::makeDirectory("{$themePath}/assets/css", 0755, true);
        File::makeDirectory("{$themePath}/assets/js", 0755, true);
        File::makeDirectory("{$themePath}/assets/images", 0755, true);

        // Create theme.json
        $config = [
            'name' => ucfirst($name) . ' Theme',
            'description' => 'A custom theme for the blog',
            'version' => '1.0.0',
            'author' => 'Unknown',
            'supports' => [
                'dark_mode' => true,
                'custom_colors' => true,
            ],
        ];

        file_put_contents(
            "{$themePath}/theme.json",
            json_encode($config, JSON_PRETTY_PRINT)
        );

        // Create basic style.css
        file_put_contents(
            "{$themePath}/style.css",
            "/* {$config['name']} Styles */\n\n.theme-wrapper.{$name} {\n    /* Custom styles for {$name} theme */\n}\n"
        );
    }

    /**
     * Publish theme assets to public directory
     */
    public function publishThemeAssets(string $theme): bool
    {
        $sourcePath = resource_path("themes/{$theme}/assets");
        $targetPath = public_path("themes/{$theme}");

        if (!is_dir($sourcePath)) {
            return false;
        }

        // Create target directory if it doesn't exist
        if (!is_dir($targetPath)) {
            File::makeDirectory($targetPath, 0755, true);
        }

        // Copy assets
        File::copyDirectory($sourcePath, $targetPath);

        return true;
    }

    /**
     * Get theme partial path
     */
    public function getThemePartialPath(string $partial): string
    {
        return "themes.{$this->activeTheme}.partials.{$partial}";
    }

    /**
     * Check if theme has a specific partial
     */
    public function hasPartial(string $partial): bool
    {
        $partialPath = "{$this->themePath}/partials/{$partial}.blade.php";
        return file_exists($partialPath);
    }
}
