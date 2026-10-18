<?php

declare(strict_types=1);

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
