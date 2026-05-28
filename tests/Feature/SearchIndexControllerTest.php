<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\SearchTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([SearchTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();

    $this->cacheDir = storage_path('app/plume/search');

    if (is_dir($this->cacheDir)) {
        foreach (glob($this->cacheDir.'/*.json') ?: [] as $file) {
            @unlink($file);
        }
    }
});

it('responds with the search index JSON', function (): void {
    $response = $this->get('/docs/_plume/search-index.json');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json');

    expect($response->headers->get('Cache-Control'))
        ->toContain('public')
        ->toContain('max-age=300')
        ->toContain('must-revalidate');

    $records = $response->json();

    expect($records)->toBeArray()
        ->and($records)->toHaveCount(3);

    $slugs = array_column($records, 'slug');

    expect($slugs)->toContain('/', 'guides/deploy', 'guides/troubleshoot');
});

it('returns each record with the expected shape', function (): void {
    $records = $this->get('/docs/_plume/search-index.json')->json();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy)
        ->toHaveKeys(['id', 'slug', 'title', 'description', 'headings', 'body', 'href', 'group'])
        ->and($deploy['title'])->toBe('Deploy to Production')
        ->and($deploy['href'])->toBe('/docs/guides/deploy');
});

it('sets an ETag header derived from the signature', function (): void {
    $response = $this->get('/docs/_plume/search-index.json');

    $etag = $response->headers->get('ETag');

    expect($etag)->toMatch('/^"[a-f0-9]{32}"$/');
});

it('returns 304 when If-None-Match matches the current ETag', function (): void {
    $first = $this->get('/docs/_plume/search-index.json');
    $etag = $first->headers->get('ETag');

    $second = $this->withHeaders(['If-None-Match' => $etag])
        ->get('/docs/_plume/search-index.json');

    expect($second->status())->toBe(304)
        ->and($second->headers->get('ETag'))->toBe($etag);
});

it('returns 200 with fresh body when If-None-Match is stale', function (): void {
    $response = $this->withHeaders(['If-None-Match' => '"deadbeef"'])
        ->get('/docs/_plume/search-index.json');

    $response->assertOk();

    expect($response->json())->toBeArray();
});

it('returns 404 for unknown vault prefix', function (): void {
    $this->get('/unknown/_plume/search-index.json')->assertNotFound();
});
