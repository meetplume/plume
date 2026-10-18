<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Meetplume\Plume\Plume;
use Tests\Fixtures\CustomizableTestVault;
use Tests\Fixtures\GuardedTestVault;

beforeEach(function (): void {
    CustomizableTestVault::$contentPath = sys_get_temp_dir().'/plume-customizer-'.uniqid();
    File::ensureDirectoryExists(CustomizableTestVault::$contentPath);
    File::put(CustomizableTestVault::$contentPath.'/index.md', "# Home\n");
});

afterEach(function (): void {
    File::deleteDirectory(CustomizableTestVault::$contentPath);
});

function bootCustomizableVaults(string ...$vaults): void
{
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults($vaults);
    app(Plume::class)->getConfiguration()->boot();
}

it('writes the theme of the vault it is posted to', function (): void {
    bootCustomizableVaults(CustomizableTestVault::class);

    $this->postJson('/custom/_plume/customizer', ['primary' => 'blue'])
        ->assertOk()
        ->assertJsonPath('primary', 'blue');

    expect(File::get(CustomizableTestVault::$contentPath.'/config.yml'))->toContain('primary: blue');

    $this->postJson('/custom/_plume/customizer/reset')
        ->assertOk()
        ->assertJsonPath('primary', 'neutral');
});

it('runs behind the vault middleware', function (): void {
    bootCustomizableVaults(GuardedTestVault::class);

    $this->postJson('/guarded/_plume/customizer', ['primary' => 'blue'])->assertStatus(418);
});
