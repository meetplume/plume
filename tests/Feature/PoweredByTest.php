<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\SearchTestVault;

it('credits Plume in the footer unless the site turns it off', function (?bool $poweredBy, bool $shown): void {
    app()->forgetInstance(Plume::class);
    $configuration = app(Plume::class)->configure()->vaults([SearchTestVault::class]);

    if ($poweredBy !== null) {
        $configuration->poweredBy($poweredBy);
    }

    $configuration->boot();

    $this->get('/docs', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.site.poweredBy', $shown);
})->with([
    'by default' => [null, true],
    'turned off' => [false, false],
]);
