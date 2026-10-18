<?php

declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Meetplume\Plume\Plume;
use Tests\Fixtures\GuardedTestVault;
use Tests\Fixtures\HostInertiaMiddleware;
use Tests\Fixtures\SearchTestVault;

function bootVaults(string ...$vaults): void
{
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults($vaults);
    app(Plume::class)->getConfiguration()->boot();
}

it('runs the vault middleware before its pages and search index', function (string $url): void {
    bootVaults(GuardedTestVault::class);

    $this->get($url)->assertStatus(418);
})->with([
    'page' => '/guarded/guides/deploy',
    'search index' => '/guarded/_plume/search-index.json',
]);

it('keeps the host inertia middleware away from plume visits', function (): void {
    app(Kernel::class)->appendMiddlewareToGroup('web', HostInertiaMiddleware::class);
    bootVaults(SearchTestVault::class);

    $this->get('/docs/guides/deploy', ['X-Inertia' => 'true'])->assertOk();
});
