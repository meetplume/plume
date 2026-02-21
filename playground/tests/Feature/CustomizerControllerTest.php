<?php

use Meetplume\Plume\Facades\Plume;
use Meetplume\Plume\ThemeConfig;

beforeEach(function (): void {
    $this->configPath = sys_get_temp_dir().'/plume_test_'.uniqid().'.yml';
    ThemeConfig::write($this->configPath, ['theme' => 'default', 'primary' => 'neutral', 'gray' => 'neutral', 'radius' => 'medium', 'spacing' => 'default', 'dark' => false]);
    Plume::config($this->configPath);
});

afterEach(function (): void {
    if (file_exists($this->configPath)) {
        unlink($this->configPath);
    }
});

it('updates config file and returns resolved values', function (): void {
    $response = $this->postJson('/_plume/customizer', [
        'primary' => 'blue',
        'radius' => 'large',
    ]);

    $response->assertSuccessful();
    $response->assertJsonFragment(['primary' => 'blue', 'radius' => 'large']);

    $config = new ThemeConfig($this->configPath);
    expect($config->toArray()['primary'])->toBe('blue');
    expect($config->toArray()['radius'])->toBe('large');
});

it('returns 422 for invalid radius', function (): void {
    $response = $this->postJson('/_plume/customizer', [
        'radius' => 'invalid',
    ]);

    $response->assertUnprocessable();
});

it('switches preset when theme sent alone', function (): void {
    $response = $this->postJson('/_plume/customizer', [
        'theme' => 'ocean',
    ]);

    $response->assertSuccessful();

    $content = file_get_contents($this->configPath);
    expect($content)->toContain('theme: ocean');
    expect($content)->not->toContain('primary:');
});

it('resets to defaults', function (): void {
    $this->postJson('/_plume/customizer', ['primary' => 'red']);

    $response = $this->postJson('/_plume/customizer/reset');

    $response->assertSuccessful();
    $response->assertJsonFragment(['primary' => 'neutral', 'radius' => 'medium']);
});

it('preserves theme key on reset', function (): void {
    $response = $this->postJson('/_plume/customizer/reset');

    $response->assertSuccessful();

    $content = file_get_contents($this->configPath);
    expect($content)->toContain('theme: default');
});
