<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Meetplume\Plume\Plume;
use Tests\Fixtures\SearchTestVault;

it('shows the logo, linking to the vault home, without a configured header', function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()
        ->logo('/logo.svg')
        ->vaults([SearchTestVault::class])
        ->boot();

    $this->get('/docs', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.header.logo', '/logo.svg')
        ->assertJsonPath('props.header.homeUrl', '/docs');
});

it('shows the brand partial a host app publishes in place of the logo and name', function (): void {
    $views = sys_get_temp_dir().'/plume-views-'.uniqid();
    File::ensureDirectoryExists($views);
    File::put($views.'/brand.blade.php', '<span class="acme">{{ $name }} · {{ $homeUrl }}</span>');
    View::prependNamespace('plume', $views);

    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->name('Acme')->vaults([SearchTestVault::class])->boot();

    $this->get('/docs', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.header.brand', '<span class="acme">Acme · /docs</span>');

    File::deleteDirectory($views);
});

it('leaves the header to the logo and name while the brand partial is empty', function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([SearchTestVault::class])->boot();

    $this->get('/docs', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.header.brand', null);
});
