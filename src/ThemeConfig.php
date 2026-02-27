<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Exception;
use Meetplume\Plume\Enums\CodeTheme;
use Symfony\Component\Yaml\Yaml;

class ThemeConfig
{
    /** @var array{primary: string, gray: string, radius: string, spacing: string, dark: bool, code_theme_light: string, code_theme_dark: string} */
    private array $resolved;

    private readonly bool $customizerEnabled;

    private readonly string $activePreset;

    private const array DEFAULTS = [
        'primary' => 'neutral',
        'gray' => 'neutral',
        'radius' => 'medium',
        'spacing' => 'default',
        'dark' => false,
        'code_theme_light' => 'github-light',
        'code_theme_dark' => 'github-dark',
    ];

    private const array VALID_RADIUS = ['none', 'small', 'medium', 'large'];

    private const array VALID_SPACING = ['dense', 'compact', 'default', 'spacious'];

    public function __construct(string $configPath, ?string $fallbackConfigPath = null)
    {
        $config = $this->parseYaml($configPath);
        $fallback = $fallbackConfigPath !== null ? $this->parseYaml($fallbackConfigPath) : [];

        $merged = array_merge($fallback, $config);

        $this->customizerEnabled = ! isset($merged['customizer']) || $merged['customizer'] !== false;
        $this->activePreset = isset($merged['theme']) && is_string($merged['theme']) ? $merged['theme'] : '';
        $this->resolved = $this->resolve($merged);
    }

    /**
     * @return array{primary: string, gray: string, radius: string, spacing: string, dark: bool, code_theme_light: string, code_theme_dark: string}
     */
    public function toArray(): array
    {
        return $this->resolved;
    }

    public function defaultDark(): bool
    {
        return $this->resolved['dark'];
    }

    public function isCustomizerEnabled(): bool
    {
        return $this->customizerEnabled;
    }

    public function activePreset(): string
    {
        return $this->activePreset;
    }

    /**
     * @return array{primary: string, gray: string, radius: string, spacing: string, dark: bool, code_theme_light: string, code_theme_dark: string}
     */
    public static function defaults(): array
    {
        return self::DEFAULTS;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function write(string $path, array $config): void
    {
        $comments = [
            'theme' => '# available values for theme: '.implode(', ', array_keys(self::presets())),
            'primary' => '# available values for primary: any tailwindcss color like (blue, red, green, yellow, ...) or arbitrary value',
            'gray' => '# available values for gray: slate, gray, zinc, neutral, stone, mauve, olive, mist, taupe',
            'radius' => '# available values for radius: '.implode(', ', self::VALID_RADIUS),
            'spacing' => '# available values for spacing: '.implode(', ', self::VALID_SPACING),
            'dark' => '# available values for dark: true, false',
            'code_theme_light' => '# available values for code_theme_light: https://expressive-code.com/guides/themes/',
            'code_theme_dark' => '# available values for code_theme_dark: https://expressive-code.com/guides/themes/',
            'customizer' => '# available values for customizer: true, false',
        ];

        $lines = [];
        foreach ($config as $key => $value) {
            if (isset($comments[$key])) {
                $lines[] = $comments[$key];
            }

            $lines[] = Yaml::dump([$key => $value], 2, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        }

        file_put_contents($path, implode("\n", $lines));
    }

    /**
     * @return array<string, array{primary: string, gray: string, radius: string, spacing: string, dark: bool, code_theme_light: string, code_theme_dark: string}>
     */
    public static function presets(): array
    {
        $presets = [];
        $dir = __DIR__.'/../resources/presets';

        if (! is_dir($dir)) {
            return $presets;
        }

        foreach (glob($dir.'/*.yml') ?: [] as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $config = new self($file);
            $presets[$name] = $config->toArray();
        }

        ksort($presets);

        return $presets;
    }

    /**
     * @return string[]
     */
    public static function validRadius(): array
    {
        return self::VALID_RADIUS;
    }

    /**
     * @return string[]
     */
    public static function validSpacing(): array
    {
        return self::VALID_SPACING;
    }

    /**
     * @return string[]
     */
    public static function validCodeThemes(): array
    {
        return array_map(fn (CodeTheme $theme) => $theme->value, CodeTheme::cases());
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{primary: string, gray: string, radius: string, spacing: string, dark: bool, code_theme_light: string, code_theme_dark: string}
     */
    private function resolve(array $config): array
    {
        $preset = $this->parseYaml($this->presetPath($config));

        $primary = $config['primary'] ?? $preset['primary'] ?? self::DEFAULTS['primary'];
        $gray = $config['gray'] ?? $preset['gray'] ?? self::DEFAULTS['gray'];
        $radius = $config['radius'] ?? $preset['radius'] ?? self::DEFAULTS['radius'];
        $spacing = $config['spacing'] ?? $preset['spacing'] ?? self::DEFAULTS['spacing'];
        $dark = $config['dark'] ?? $preset['dark'] ?? self::DEFAULTS['dark'];
        $codeThemeLight = $config['code_theme_light'] ?? $preset['code_theme_light'] ?? self::DEFAULTS['code_theme_light'];
        $codeThemeDark = $config['code_theme_dark'] ?? $preset['code_theme_dark'] ?? self::DEFAULTS['code_theme_dark'];

        $validCodeThemes = self::validCodeThemes();

        return [
            'primary' => is_string($primary) ? $primary : self::DEFAULTS['primary'],
            'gray' => is_string($gray) ? $gray : self::DEFAULTS['gray'],
            'radius' => is_string($radius) && in_array($radius, self::VALID_RADIUS, true) ? $radius : self::DEFAULTS['radius'],
            'spacing' => is_string($spacing) && in_array($spacing, self::VALID_SPACING, true) ? $spacing : self::DEFAULTS['spacing'],
            'dark' => is_bool($dark) ? $dark : self::DEFAULTS['dark'],
            'code_theme_light' => is_string($codeThemeLight) && in_array($codeThemeLight, $validCodeThemes, true) ? $codeThemeLight : self::DEFAULTS['code_theme_light'],
            'code_theme_dark' => is_string($codeThemeDark) && in_array($codeThemeDark, $validCodeThemes, true) ? $codeThemeDark : self::DEFAULTS['code_theme_dark'],
        ];
    }

    private function presetPath(mixed $config): string
    {
        if (! is_array($config) || ! isset($config['theme']) || ! is_string($config['theme'])) {
            return '';
        }

        return __DIR__.'/../resources/presets/'.basename($config['theme']).'.yml';
    }

    /**
     * @return array<string, mixed>
     */
    private function parseYaml(string $path): array
    {
        if ($path === '' || ! file_exists($path)) {
            return [];
        }

        try {
            $parsed = Yaml::parse((string) file_get_contents($path));
        } catch (Exception) {
            return [];
        }

        return is_array($parsed) ? array_filter($parsed, is_string(...), ARRAY_FILTER_USE_KEY) : [];
    }
}
