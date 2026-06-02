<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function (): void {
    File::deleteDirectory(public_path('vendor/plume'));
});

it('removes stale assets and republishes the current ones', function (): void {
    $stale = public_path('vendor/plume/dist/assets/old-chunk.js');
    File::ensureDirectoryExists(dirname($stale));
    File::put($stale, '// stale build');

    $this->artisan('plume:update-assets')
        ->assertSuccessful();

    expect(File::exists($stale))->toBeFalse()
        ->and(File::exists(public_path('vendor/plume/dist/manifest.json')))->toBeTrue();
});

it('succeeds when no assets have been published yet', function (): void {
    File::deleteDirectory(public_path('vendor/plume'));

    $this->artisan('plume:update-assets')
        ->assertSuccessful();
});
