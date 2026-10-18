<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\AppLanguageTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([AppLanguageTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();
});

it('serves the language the app picks, with no switcher and no language in its links', function (): void {
    app()->setLocale('pt');

    $this->get('/app-language/guides/deploy', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.title', 'Publicar')
        ->assertJsonPath('props.pageBaseUrl', '/app-language/guides')
        ->assertJsonMissingPath('props.languages');
});

it('has no language segment in its routes', function (): void {
    $this->get('/app-language/pt/guides/deploy', ['X-Inertia' => 'true'])->assertNotFound();
});

it('fills in a page the app language lacks from the default language', function (): void {
    app()->setLocale('pt');

    $response = $this->get('/app-language/guides/english-only', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.title', 'English only')
        ->assertJsonPath('props.contentAssetBase', '/app-language/_content/en/guides');

    $labels = collect($response->json('props.navigation'))->flatMap(fn (array $group): array => array_column($group['pages'], 'label'));

    expect($labels)->toContain('Publicar', 'English only');
});

it('serves the default language for a locale the vault does not offer', function (): void {
    app()->setLocale('de');

    $this->get('/app-language/guides/deploy', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.title', 'Deploying');
});

it('indexes the filled-in pages for search, linked without language', function (): void {
    $records = collect($this->get('/app-language/_plume/search-index.json?lang=pt')->assertOk()->json());

    expect($records->firstWhere('slug', 'guides/english-only')['href'])->toBe('/app-language/guides/english-only')
        ->and($records->firstWhere('slug', 'guides/deploy')['title'])->toBe('Publicar');
});
