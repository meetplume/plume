<?php

declare(strict_types=1);

namespace Meetplume\Plume\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Meetplume\Plume\Collection;
use Meetplume\Plume\Plume;
use Meetplume\Plume\ThemeConfig;
use Symfony\Component\Yaml\Yaml;

class CustomizerController
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['sometimes', 'string'],
            'primary' => ['sometimes', 'string'],
            'gray' => ['sometimes', 'string'],
            'radius' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validRadius())],
            'spacing' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validSpacing())],
            'dark' => ['sometimes', 'boolean'],
            'code_theme_light' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validCodeThemes())],
            'code_theme_dark' => ['sometimes', 'string', 'in:'.implode(',', ThemeConfig::validCodeThemes())],
            'collection' => ['sometimes', 'nullable', 'string'],
        ]);

        $plume = app(Plume::class);
        $globalConfigPath = $plume->configPath();
        /** @var ?string $collectionPrefix */
        $collectionPrefix = $validated['collection'] ?? null;
        unset($validated['collection']);

        [$configPath, $fallbackConfigPath] = $this->resolveConfigPaths($plume, $globalConfigPath, $collectionPrefix);

        if ($configPath === null) {
            return response()->json(['error' => 'No config path configured'], 422);
        }

        // When only theme (preset) is sent, rewrite config as just the theme key
        if (isset($validated['theme']) && count($validated) === 1) {
            $config = ['theme' => $validated['theme']];
        } else {
            $existing = $this->readExistingConfig($configPath);
            /** @var array<string, mixed> $config */
            $config = array_merge($existing, $validated);
        }

        ThemeConfig::write($configPath, $config);

        $themeConfig = new ThemeConfig($configPath, $fallbackConfigPath);
        app()->instance(ThemeConfig::class, $themeConfig);

        return response()->json($themeConfig->toArray());
    }

    public function reset(Request $request): JsonResponse
    {
        $plume = app(Plume::class);
        $globalConfigPath = $plume->configPath();
        /** @var ?string $collectionPrefix */
        $collectionPrefix = $request->input('collection');

        [$configPath, $fallbackConfigPath] = $this->resolveConfigPaths($plume, $globalConfigPath, $collectionPrefix);

        if ($configPath === null) {
            return response()->json(['error' => 'No config path configured'], 422);
        }

        $existing = $this->readExistingConfig($configPath);
        $config = ThemeConfig::defaults();

        // Preserve theme key if it exists
        if (isset($existing['theme'])) {
            $config = ['theme' => $existing['theme'], ...$config];
        }

        ThemeConfig::write($configPath, $config);

        $themeConfig = new ThemeConfig($configPath, $fallbackConfigPath);
        app()->instance(ThemeConfig::class, $themeConfig);

        return response()->json($themeConfig->toArray());
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function resolveConfigPaths(Plume $plume, ?string $globalConfigPath, ?string $collectionPrefix): array
    {
        if ($collectionPrefix !== null) {
            $collection = $plume->getCollection($collectionPrefix);

            if ($collection instanceof Collection && $collection->getConfigPath() !== null) {
                return [$collection->getConfigPath(), $globalConfigPath];
            }
        }

        return [$globalConfigPath, null];
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
