<?php

declare(strict_types=1);

use Meetplume\Plume\Actions\SearchIndexBuilder;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Vault;

$fixturePath = __DIR__.'/../fixtures/search';

function makeSearchVault(string $absolutePath): Vault
{
    return new class($absolutePath) extends Vault
    {
        public function __construct(string $absolutePath)
        {
            $reflection = new ReflectionClass($this);

            foreach (['path' => $absolutePath, 'prefix' => '/docs', 'discovery' => Discovery::Auto] as $property => $value) {
                $prop = $reflection->getProperty($property);
                $prop->setAccessible(true);
                $prop->setValue($this, $value);
            }
        }
    };
}

it('builds records for every non-hidden page', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();

    $slugs = array_column($records, 'slug');

    expect($records)->toHaveCount(3)
        ->and($slugs)->toContain('/', 'guides/deploy', 'guides/troubleshoot');
});

it('uses frontmatter title when present', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['title'])->toBe('Deploy to Production');
});

it('falls back to first H1 when frontmatter title is missing', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $troubleshoot = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($troubleshoot['title'])->toBe('Troubleshooting');
});

it('reads description from frontmatter', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['description'])->toBe('Steps to ship Plume to production');
});

it('returns empty description when frontmatter has none', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $troubleshoot = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($troubleshoot['description'])->toBe('');
});

it('extracts H1 through H3 headings', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['headings'])->toBe([
        'Deploy to Production',
        'Prerequisites',
        'Web Server',
        'Steps',
    ]);
});

it('does not extract headings from inside code fences', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');

    expect($home['headings'])->toBe(['Welcome', 'Why Plume?', 'Features']);
});

it('strips markdown to plaintext for the body field', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');

    expect($home['body'])
        ->not->toContain('**', '`', '#', '[installation]', '](', 'composer require')
        ->toContain('Get started with Plume')
        ->toContain('installation guide')
        ->toContain('Vaults');
});

it('removes images and preserves blockquote text', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $trouble = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($trouble['body'])
        ->not->toContain('![', 'flow.png')
        ->toContain('Tip: enable debug mode');
});

it('builds href from prefix and slug', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($home['href'])->toBe('/docs')
        ->and($deploy['href'])->toBe('/docs/guides/deploy');
});

it('groups pages by their navigation group label', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = (new SearchIndexBuilder($vault))->build();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['group'])->toBe('Guides');
});

it('produces a stable signature for the same files', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $builder = new SearchIndexBuilder($vault);
    $a = $builder->signature();
    $b = $builder->signature();

    expect($a)->toBe($b)
        ->and($a)->toHaveLength(32);
});

it('changes signature when a source file is touched', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);
    $builder = new SearchIndexBuilder($vault);

    $before = $builder->signature();

    $file = $fixturePath.'/guides/deploy.md';
    $originalMtime = filemtime($file);
    touch($file, $originalMtime + 5);

    $after = $builder->signature();

    touch($file, $originalMtime);

    expect($after)->not->toBe($before);
});
