<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\LocalizedTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([LocalizedTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();
});

it('serves each language home', function (string $url, string $title): void {
    $this->get($url, ['X-Inertia' => 'true'])->assertOk()->assertJsonPath('props.title', $title);
})->with([
    'default language at the bare prefix' => ['/localized', 'English home'],
    'another language under its code' => ['/localized/pt', 'Pagina inicial'],
]);

it('serves discovered pages from their language folder', function (string $url, string $title): void {
    $this->get($url, ['X-Inertia' => 'true'])->assertOk()->assertJsonPath('props.title', $title);
})->with([
    'default language without its code' => ['/localized/guides/deploy', 'Deploying'],
    'another language under its code' => ['/localized/pt/guides/deploy', 'Publicar'],
]);
