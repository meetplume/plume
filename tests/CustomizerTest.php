<?php

use Meetplume\Plume\ThemeConfig;

it('returns expected defaults', function (): void {
    $defaults = ThemeConfig::defaults();

    expect($defaults)->toBe([
        'primary' => 'neutral',
        'gray' => 'neutral',
        'radius' => 'medium',
        'spacing' => 'default',
        'dark' => false,
    ]);
});

it('writes valid YAML', function (): void {
    $path = sys_get_temp_dir().'/plume_test_'.uniqid().'.yml';

    ThemeConfig::write($path, [
        'primary' => 'blue',
        'gray' => 'slate',
        'radius' => 'small',
        'spacing' => 'compact',
        'dark' => true,
    ]);

    expect(file_exists($path))->toBeTrue();

    $config = new ThemeConfig($path);

    expect($config->toArray())->toBe([
        'primary' => 'blue',
        'gray' => 'slate',
        'radius' => 'small',
        'spacing' => 'compact',
        'dark' => true,
    ]);

    unlink($path);
});

it('reads customizer enabled by default', function (): void {
    $path = sys_get_temp_dir().'/plume_test_'.uniqid().'.yml';
    ThemeConfig::write($path, ['primary' => 'blue']);

    $config = new ThemeConfig($path);

    expect($config->isCustomizerEnabled())->toBeTrue();

    unlink($path);
});

it('reads customizer disabled when set to false', function (): void {
    $path = sys_get_temp_dir().'/plume_test_'.uniqid().'.yml';
    ThemeConfig::write($path, ['customizer' => false, 'primary' => 'blue']);

    $config = new ThemeConfig($path);

    expect($config->isCustomizerEnabled())->toBeFalse();

    unlink($path);
});

it('reads active preset from theme key', function (): void {
    $config = new ThemeConfig(__DIR__.'/fixtures/config.yml');

    expect($config->activePreset())->toBe('ocean');
});

it('returns empty preset when no theme key', function (): void {
    $path = sys_get_temp_dir().'/plume_test_'.uniqid().'.yml';
    ThemeConfig::write($path, ['primary' => 'blue']);

    $config = new ThemeConfig($path);

    expect($config->activePreset())->toBe('');

    unlink($path);
});

it('lists available presets with config', function (): void {
    $presets = ThemeConfig::presets();

    expect($presets)->toHaveKeys(['default', 'brutalist', 'catppuccin', 'forest', 'ocean', 'rose']);
    expect($presets['ocean'])->toBe(['primary' => 'blue', 'gray' => 'slate', 'dark' => false]);
    expect($presets['catppuccin'])->toHaveKey('dark', true);
});

it('stores and returns config path on Plume singleton', function (): void {
    $plume = new \Meetplume\Plume\Plume;

    expect($plume->configPath())->toBeNull();
});
