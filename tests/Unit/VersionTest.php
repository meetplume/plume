<?php

use Meetplume\Plume\Tab;
use Meetplume\Plume\Version;

it('creates a version with make()', function (): void {
    $version = Version::make('v2');

    expect($version->key)->toBe('v2');
});

it('marks version as default', function (): void {
    $version = Version::make('v2')->default();

    expect($version->isDefault())->toBeTrue();
});

it('is not default by default', function (): void {
    $version = Version::make('v1');

    expect($version->isDefault())->toBeFalse();
});

it('stores per-version tabs', function (): void {
    $version = Version::make('v2')->tabs([
        Tab::make('documentation'),
        Tab::make('api'),
    ]);

    expect($version->hasTabs())->toBeTrue()
        ->and($version->getTabs())->toHaveCount(2);
});

it('returns null tabs when not set', function (): void {
    $version = Version::make('v1');

    expect($version->hasTabs())->toBeFalse()
        ->and($version->getTabs())->toBeNull();
});
