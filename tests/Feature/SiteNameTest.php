<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Meetplume\Plume\Plume;
use Tests\Fixtures\SearchTestVault;

it('names the site on each request when the name is a closure', function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()
        ->name(fn (Request $request): string => 'Docs of '.$request->getHost())
        ->vaults([SearchTestVault::class])
        ->boot();

    $this->get('http://acme.test/docs', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.site.name', 'Docs of acme.test');

    $this->get('http://globex.test/docs', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.site.name', 'Docs of globex.test');
});
