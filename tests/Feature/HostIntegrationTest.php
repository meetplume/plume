<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Meetplume\Plume\Plume;
use Tests\Fixtures\SearchTestVault;

beforeEach(function (): void {
    // A manifest of our own, so the page renders without a published build.
    $this->publicPath = sys_get_temp_dir().'/plume-public-'.uniqid();
    File::ensureDirectoryExists($this->publicPath.'/vendor/plume/dist');
    File::put($this->publicPath.'/vendor/plume/dist/manifest.json', json_encode([
        'resources/js/app.tsx' => ['file' => 'assets/app.js', 'src' => 'resources/js/app.tsx', 'isEntry' => true],
    ]));
    app()->usePublicPath($this->publicPath);

    app()->forgetInstance(Plume::class);
});

afterEach(function (): void {
    File::deleteDirectory($this->publicPath);
});

it('hands the host app light/dark key to the page, which has a doctype', function (): void {
    app(Plume::class)->configure()->appearanceKey('appearance')->vaults([SearchTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();

    $this->get('/docs')
        ->assertOk()
        ->assertSee('<!DOCTYPE html>', false)
        ->assertSee('data-appearance-key="appearance"', false);
});

it('renders the head partial a host app publishes', function (): void {
    $views = sys_get_temp_dir().'/plume-views-'.uniqid();
    File::ensureDirectoryExists($views);
    File::put($views.'/head.blade.php', '<meta name="host-head">');
    View::prependNamespace('plume', $views);

    app(Plume::class)->configure()->vaults([SearchTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();

    $this->get('/docs')->assertOk()->assertSee('<meta name="host-head">', false);

    File::deleteDirectory($views);
});
