<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\ForbiddenTestVault;
use Tests\Fixtures\HandbookTestVault;
use Tests\Fixtures\SearchTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([SearchTestVault::class, ForbiddenTestVault::class, HandbookTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();
});

it('serves the Markdown of a page, without its frontmatter', function (): void {
    $response = $this->get('/docs/guides/deploy.md');

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');

    expect($response->getContent())
        ->toStartWith('# ')
        ->not->toContain('title:');
});

it('serves the Markdown of the vault home at the prefix followed by .md', function (): void {
    expect($this->get('/docs.md')->assertOk()->getContent())
        ->toStartWith("# Welcome\n\nGet started with **Plume**");
});

it('serves the Markdown of a page in each of its addresses', function (string $uri, string $start): void {
    expect($this->get($uri)->assertOk()->getContent())->toStartWith($start);
})->with([
    'language' => ['/handbook/fr/guides/onboarding.md', '# Arrivée'],
    'default language' => ['/handbook/guides/onboarding.md', '# Onboarding'],
    'home in the default language' => ['/handbook.md', '# Handbook'],
    'home in a language' => ['/handbook/fr.md', '# Manuel'],
    'absolute route' => ['/about.md', '# About'],
]);

it('keeps the thematic breaks of the page', function (): void {
    expect($this->get('/handbook/guides/onboarding.md')->getContent())
        ->toBe("# Onboarding\n\nMeet the team on Monday.\n\n---\n\nShip something on Friday.\n");
});

it('returns 404 for the Markdown of a page that does not exist', function (string $uri): void {
    $this->get($uri)->assertNotFound();
})->with([
    'unknown page' => '/docs/guides/missing.md',
    'unknown language' => '/handbook/de/guides/onboarding.md',
    'page without its .md' => '/docs/guides/deploy.markdown',
]);

it('returns 403 for the Markdown of a page when the vault denies access', function (string $uri): void {
    $this->get($uri)->assertForbidden();
})->with([
    'page' => '/forbidden/guides/deploy.md',
    'vault home' => '/forbidden.md',
]);

it('gives the page its actions and the address of its Markdown', function (): void {
    $this->get('/docs/guides/deploy', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.pageActions', ['copy', 'markdown', 'chatgpt', 'claude', 'pdf'])
        ->assertJsonPath('props.markdownUrl', '/docs/guides/deploy.md');
});

it('gives the vault home the address of its Markdown', function (): void {
    $this->get('/docs', ['X-Inertia' => 'true'])
        ->assertJsonPath('props.markdownUrl', '/docs.md');
});

it('leaves out the actions a vault does not offer', function (): void {
    $this->get('/handbook/fr/guides/onboarding', ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.pageActions', ['copy', 'markdown', 'pdf'])
        ->assertJsonPath('props.markdownUrl', '/handbook/fr/guides/onboarding.md');
});

it('words the page menu in the language of the page', function (): void {
    $texts = $this->get('/handbook/fr/guides/onboarding', ['X-Inertia' => 'true'])->assertOk()->json('props.texts');

    expect($texts)
        ->toHaveKey('Copy page', 'Copier la page')
        ->toHaveKey('Read :url and answer questions about the content.', 'Lis :url et réponds aux questions sur son contenu.');
});
