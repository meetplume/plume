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

it('serves the assets of a page from its language folder', function (): void {
    $this->get('/localized/pt/guides/deploy', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.contentAssetBase', '/localized/_content/pt/guides')
        ->assertJsonPath('props.pageBaseUrl', '/localized/pt/guides');
});

it('words its interface in the language of the page', function (string $url, string $locale, string $tableOfContents): void {
    $this->get($url, ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.locale', $locale)
        ->assertJsonPath('props.texts.On this page', $tableOfContents);
})->with([
    'English, the source of the texts' => ['/localized', 'en', 'On this page'],
    'a language Plume translates' => ['/localized/pt', 'pt', 'Nesta página'],
]);
