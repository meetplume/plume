<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Meetplume\Plume\Plume;
use Meetplume\Plume\ThemeConfig;
use Symfony\Component\Yaml\Yaml;

class CustomizerController
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vault' => ['required', 'string'],
            'theme' => ['sometimes', 'string'],
            'primary' => ['sometimes', 'string'],
            'gray' => ['sometimes', 'string'],
            'radius' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validRadius())],
            'spacing' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validSpacing())],
            'dark' => ['sometimes', 'boolean'],
            'code_theme_light' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validCodeThemes())],
            'code_theme_dark' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validCodeThemes())],
        ]);

        $configPath = $this->resolveConfigPath($validated['vault']);
        unset($validated['vault']);

        if ($configPath === null) {
            return response()->json(['error' => 'No config path configured'], 422);
        }

        if (isset($validated['theme']) && count($validated) === 1) {
            $config = ['theme' => $validated['theme']];
        } else {
            $existing = $this->readExistingConfig($configPath);
            /** @var array<string, mixed> $config */
            $config = array_merge($existing, $validated);
        }

        ThemeConfig::write($configPath, $config);

        $themeConfig = new ThemeConfig($configPath);
        app()->instance(ThemeConfig::class, $themeConfig);

        return response()->json($themeConfig->toArray());
    }

    public function reset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vault' => ['required', 'string'],
        ]);

        $configPath = $this->resolveConfigPath($validated['vault']);

        if ($configPath === null) {
            return response()->json(['error' => 'No config path configured'], 422);
        }

        $existing = $this->readExistingConfig($configPath);
        $config = ThemeConfig::defaults();

        if (isset($existing['theme'])) {
            $config = ['theme' => $existing['theme'], ...$config];
        }

        ThemeConfig::write($configPath, $config);

        $themeConfig = new ThemeConfig($configPath);
        app()->instance(ThemeConfig::class, $themeConfig);

        return response()->json($themeConfig->toArray());
    }

    private function resolveConfigPath(string $vaultPrefix): ?string
    {
        $config = app(Plume::class)->getConfiguration();

        if ($config === null) {
            return null;
        }

        $vault = $config->getVault($vaultPrefix);

        if ($vault === null) {
            return null;
        }

        return $vault->getConfigPath();
    }

    /**
     * @return array<string, mixed>
     */
    private function readExistingConfig(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        /** @var array<string, mixed>|null $parsed */
        $parsed = Yaml::parse((string) file_get_contents($path));

        return is_array($parsed) ? $parsed : [];
    }
}
