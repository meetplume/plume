<?php

namespace App\Services;

use Exception;
use ZipArchive;
use ValueError;
use App\Enums\SiteSettings;
use Illuminate\Support\Facades\File;
use Rawilk\Settings\Support\Context;
use Illuminate\Support\Facades\Storage;

class ThemeService
{
    protected string $activeTheme;
    protected string $themePath;

    public function __construct()
    {
        $this->activeTheme = SiteSettings::ACTIVE_THEME->get() ?? 'default';
        $this->themePath = $this->getThemeDirectory($this->activeTheme) ?? resource_path("themes/{$this->activeTheme}");
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

    public function getThemeSettings(): ?array
    {
        $config = $this->getThemeConfig();
        return $config['settings'] ?? null;
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
        $settings = $this->getThemeSettings();

        if (!isset($settings)) {
            return;
        }

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                // TODO: Handle complex settings
                continue; // Skip complex settings for now
            }

            try {
                $setting = SiteSettings::from($key);
                $setting->set($value);
            } catch (ValueError) {
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

        // Get themes from resources/themes
        $resourceThemesPath = resource_path('themes');
        if (is_dir($resourceThemesPath)) {
            foreach (glob("{$resourceThemesPath}/*", GLOB_ONLYDIR) as $themeDir) {
                $themeName = basename($themeDir);
                $configPath = "{$themeDir}/theme.json";

                if (file_exists($configPath)) {
                    $config = json_decode(file_get_contents($configPath), true);
                    if ($config) {
                        $config['source'] = 'resources';
                        $themes[$themeName] = $config;
                    }
                } else {
                    // Theme without config file - add basic info
                    $themes[$themeName] = [
                        'name' => ucfirst($themeName),
                        'description' => 'Theme without configuration',
                        'version' => '1.0.0',
                        'source' => 'resources',
                    ];
                }
            }
        }

        // Get themes from storage/app/private/themes
        $privateThemesPath = storage_path('app/private/themes');
        if (is_dir($privateThemesPath)) {
            foreach (glob("{$privateThemesPath}/*", GLOB_ONLYDIR) as $themeDir) {
                $themeName = basename($themeDir);
                $configPath = "{$themeDir}/theme.json";

                if (file_exists($configPath)) {
                    $config = json_decode(file_get_contents($configPath), true);
                    if ($config) {
                        $config['source'] = 'uploaded';
                        $themes[$themeName] = $config;
                    }
                } else {
                    // Theme without config file - add basic info
                    $themes[$themeName] = [
                        'name' => ucfirst($themeName),
                        'description' => 'Uploaded theme without configuration',
                        'version' => '1.0.0',
                        'source' => 'uploaded',
                    ];
                }
            }
        }

        return $themes;
    }

    /**
     * Check if a theme exists
     */
    public function themeExists(string $theme): bool
    {
        return is_dir(resource_path("themes/{$theme}")) ||
               is_dir(storage_path("app/private/themes/{$theme}"));
    }

    /**
     * Get the full path to a theme directory
     */
    public function getThemeDirectory(string $theme): ?string
    {
        $resourcePath = resource_path("themes/{$theme}");
        if (is_dir($resourcePath)) {
            return $resourcePath;
        }

        $privatePath = storage_path("app/private/themes/{$theme}");
        if (is_dir($privatePath)) {
            return $privatePath;
        }

        return null;
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
        $this->themePath = $this->getThemeDirectory($theme) ?? resource_path("themes/{$theme}");

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
        $themeFieldsService = app(ThemeFieldsService::class);
        $fields = $themeFieldsService->getThemeFields($theme);

        foreach ($fields as $field) {
            $fieldKey = $field['key'] ?? '';
            $defaultValue = $field['default'] ?? null;

            if ($fieldKey && $defaultValue !== null) {
                $settingKey = "theme_{$theme}_{$fieldKey}";

                // Only set default if no value exists yet
                $existingValue = settings($settingKey);
                if ($existingValue === null) {
                    settings()->context(new Context([]))->set($settingKey, $defaultValue);
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

    /**
     * Upload and install a theme from a ZIP file
     */
    public function uploadAndInstallTheme(string $filePath): array
    {
        try {
            // Get the full path to the uploaded file
            $fullPath = Storage::disk('local')->path($filePath);

            if (!file_exists($fullPath)) {
                return ['success' => false, 'message' => 'Uploaded file not found.'];
            }

            // Create a temporary directory for extraction
            $tempDir = storage_path('app/temp/' . uniqid('theme_'));
            File::makeDirectory($tempDir, 0755, true);

            // Extract the ZIP file
            $zip = new ZipArchive();
            $result = $zip->open($fullPath);

            if ($result !== TRUE) {
                File::deleteDirectory($tempDir);
                return ['success' => false, 'message' => 'Could not open ZIP file. Please ensure the file is a valid ZIP archive.'];
            }

            $zip->extractTo($tempDir);
            $zip->close();

            // Find the theme directory (could be nested)
            $themeDir = $this->findThemeDirectory($tempDir);
            if (!$themeDir) {
                File::deleteDirectory($tempDir);
                return ['success' => false, 'message' => 'No valid theme found. Please ensure the ZIP contains a theme.json file.'];
            }

            // Validate theme structure
            $validation = $this->validateThemeStructure($themeDir);
            if (!$validation['valid']) {
                File::deleteDirectory($tempDir);
                return ['success' => false, 'message' => $validation['message']];
            }

            // Get theme name from theme.json
            $themeConfig = json_decode(file_get_contents($themeDir . '/theme.json'), true);
            $themeName = $this->generateThemeName($themeConfig);

            // Create final theme directory
            $finalThemeDir = storage_path("app/private/themes/{$themeName}");

            if (is_dir($finalThemeDir)) {
                File::deleteDirectory($tempDir);
                return ['success' => false, 'message' => "A theme with the name '{$themeName}' already exists."];
            }

            // Create private themes directory if it doesn't exist
            File::makeDirectory(storage_path('app/private/themes'), 0755, true, true);

            // Move theme to final location
            File::moveDirectory($themeDir, $finalThemeDir);

            // Clean up
            File::deleteDirectory($tempDir);
            Storage::disk('local')->delete($filePath);

            return [
                'success' => true,
                'theme_name' => $themeConfig['name'] ?? ucfirst($themeName),
                'theme_key' => $themeName
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred during theme installation: ' . $e->getMessage()];
        }
    }

    /**
     * Find the theme directory in extracted files
     */
    private function findThemeDirectory(string $baseDir): ?string
    {
        // Check if theme.json is in the root
        if (file_exists($baseDir . '/theme.json')) {
            return $baseDir;
        }

        // Look for theme.json in subdirectories (one level deep)
        $directories = glob($baseDir . '/*', GLOB_ONLYDIR);
        foreach ($directories as $dir) {
            if (file_exists($dir . '/theme.json')) {
                return $dir;
            }
        }

        return null;
    }

    /**
     * Validate theme structure
     */
    private function validateThemeStructure(string $themeDir): array
    {
        if (!file_exists($themeDir . '/theme.json')) {
            return ['valid' => false, 'message' => 'Theme must contain a theme.json file.'];
        }

        $themeConfig = json_decode(file_get_contents($themeDir . '/theme.json'), true);
        if (!$themeConfig) {
            return ['valid' => false, 'message' => 'Invalid theme.json file. Please ensure it contains valid JSON.'];
        }

        if (empty($themeConfig['name'])) {
            return ['valid' => false, 'message' => 'Theme must have a name in theme.json.'];
        }

        return ['valid' => true];
    }

    /**
     * Generate a unique theme name/key
     */
    private function generateThemeName(array $themeConfig): string
    {
        $baseName = $themeConfig['name'] ?? 'custom-theme';
        $themeName = str($baseName)->slug();

        // Ensure uniqueness
        $counter = 1;
        $originalName = $themeName;
        while ($this->themeExists($themeName) || is_dir(storage_path("app/private/themes/{$themeName}"))) {
            $themeName = $originalName . '-' . $counter;
            $counter++;
        }

        return $themeName;
    }
}
