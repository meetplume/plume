<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Exception;
use Symfony\Component\Yaml\Yaml;

class ThemeConfig
{
    /** @var array{primary: string, gray: string, radius: string, spacing: string, dark: bool} */
    private array $resolved;

    private const array DEFAULTS = [
        'primary' => 'neutral',
        'gray' => 'neutral',
        'radius' => 'medium',
        'spacing' => 'default',
        'dark' => false,
    ];

    private const array VALID_RADIUS = ['none', 'small', 'medium', 'large'];

    private const array VALID_SPACING = ['dense', 'compact', 'default', 'spacious'];

    public function __construct(string $configPath)
    {
        $this->resolved = $this->resolve($configPath);
    }

    /**
     * @return array{primary: string, gray: string, radius: string, spacing: string, dark: bool}
     */
    public function toArray(): array
    {
        return $this->resolved;
    }

    public function defaultDark(): bool
    {
        return $this->resolved['dark'];
    }

    /**
     * @return array{primary: string, gray: string, radius: string, spacing: string, dark: bool}
     */
    private function resolve(string $configPath): array
    {
        $config = $this->parseYaml($configPath);
        $preset = $this->parseYaml($this->presetPath($config));

        $primary = $config['primary'] ?? $preset['primary'] ?? self::DEFAULTS['primary'];
        $gray = $config['gray'] ?? $preset['gray'] ?? self::DEFAULTS['gray'];
        $radius = $config['radius'] ?? $preset['radius'] ?? self::DEFAULTS['radius'];
        $spacing = $config['spacing'] ?? $preset['spacing'] ?? self::DEFAULTS['spacing'];
        $dark = $config['dark'] ?? $preset['dark'] ?? self::DEFAULTS['dark'];

        return [
            'primary' => is_string($primary) ? $primary : self::DEFAULTS['primary'],
            'gray' => is_string($gray) ? $gray : self::DEFAULTS['gray'],
            'radius' => is_string($radius) && in_array($radius, self::VALID_RADIUS, true) ? $radius : self::DEFAULTS['radius'],
            'spacing' => is_string($spacing) && in_array($spacing, self::VALID_SPACING, true) ? $spacing : self::DEFAULTS['spacing'],
            'dark' => is_bool($dark) ? $dark : self::DEFAULTS['dark'],
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
