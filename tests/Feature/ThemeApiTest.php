<?php

use App\Services\Theme;
use App\Services\ThemeFields;

test('theme helper function exists and returns Theme instance', function () {
    expect(function_exists('theme'))->toBeTrue()
        ->and(theme())->toBeInstanceOf(Theme::class);
});

test('theme active method returns active theme', function () {
    expect(theme()->active())->toBeString()
        ->and(theme()->active())->toBe('default');
});

test('theme config method returns array', function () {
    expect(theme()->config())->toBeArray();
});

test('theme exists method works correctly', function () {
    expect(theme()->exists('default'))->toBeTrue()
        ->and(theme()->exists('nonexistent-theme'))->toBeFalse();
});

test('theme fields method returns ThemeFields instance', function () {
    $fields = theme()->fields('theme_magazine_accent_color');
    expect($fields)->toBeInstanceOf(ThemeFields::class);
});

test('theme fields default method returns default value', function () {
    // Test with a magazine theme field
    $this->artisan('theme:activate magazine');

    $defaultValue = theme()->fields('theme_magazine_accent_color')->default();
    expect($defaultValue)->toBe('#f59e0b'); // From magazine theme.json
});

test('theme fields value method returns current value', function () {
    // Set a value first, then test retrieval
    theme()->fields('theme_magazine_accent_color')->set('#ff0000');
    $value = theme()->fields('theme_magazine_accent_color')->value();
    expect($value)->toBe('#ff0000');
});

test('theme fields key method returns correct setting key', function () {
    $key = theme()->fields('theme_magazine_accent_color')->key();
    expect($key)->toContain('theme_')
        ->and($key)->toContain('accent_color');
});

test('theme fields definition method returns field config', function () {
    $this->artisan('theme:activate magazine');

    $definition = theme()->fields('theme_magazine_accent_color')->definition();
    expect($definition)->toBeArray()
        ->and($definition)->toHaveKey('key')
        ->and($definition)->toHaveKey('type')
        ->and($definition[ 'key' ])->toBe('accent_color');
});

test('theme fields exists method works correctly', function () {
    $this->artisan('theme:activate magazine');

    expect(theme()->fields('theme_magazine_accent_color')->exists())->toBeTrue()
        ->and(theme()->fields('theme_magazine_nonexistent')->exists())->toBeFalse();
});
