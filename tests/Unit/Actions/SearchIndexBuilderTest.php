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
                $prop->setValue($this, $value);
            }
        }
    };
}

it('builds records for every non-hidden page', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();

    $slugs = array_column($records, 'slug');

    expect($records)->toHaveCount(3)
        ->and($slugs)->toContain('/', 'guides/deploy', 'guides/troubleshoot');
});

it('uses frontmatter title when present', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['title'])->toBe('Deploy to Production');
});

it('falls back to first H1 when frontmatter title is missing', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $troubleshoot = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($troubleshoot['title'])->toBe('Troubleshooting');
});

it('reads description from frontmatter', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['description'])->toBe('Steps to ship Plume to production');
});

it('returns empty description when frontmatter has none', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $troubleshoot = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($troubleshoot['description'])->toBe('');
});

it('extracts H1 through H3 headings', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
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

    $records = new SearchIndexBuilder($vault)->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');

    expect($home['headings'])->toBe(['Welcome', 'Why Plume?', 'Features']);
});

it('strips markdown to plaintext for the body field', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');

    expect($home['body'])
        ->not->toContain('**')
        ->not->toContain('`')
        ->not->toContain('#')
        ->not->toContain('[installation]')
        ->not->toContain('](')
        ->not->toContain('composer require')
        ->toContain('Get started with Plume')
        ->toContain('installation guide')
        ->toContain('Vaults');
});

it('keeps the text of inline code', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($home['body'])->toContain('It is a fast and simple markdown tool.')
        ->and($deploy['body'])->toContain('Run composer install')
        ->and($deploy['body'])->toContain('caddy reload --config <file>');
});

it('flattens tables to the text of their cells', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['body'])
        ->not->toContain('|')
        ->not->toContain(':-')
        ->not->toContain('---')
        ->toContain('works great. Server Reload with Nginx nginx -s reload Caddy caddy reload --config <file> Steps');
});

it('removes images and preserves blockquote text', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $trouble = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($trouble['body'])
        ->not->toContain('![')
        ->not->toContain('flow.png')
        ->toContain('Tip: enable debug mode');
});

it('keeps callout titles and text without the directive markers', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $trouble = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($trouble['body'])
        ->not->toContain(':::')
        ->not->toContain('[!NOTE]')
        ->not->toContain('warning')
        ->not->toContain('details')
        ->toContain('Read the logs Laravel writes every error to')
        ->toContain('Stale config Clear the config cache')
        ->toContain('Queue workers keep old code in memory.')
        ->toContain('Restart them after each deploy.');
});

it('drops tilde and nested code fences', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $trouble = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/troubleshoot');

    expect($trouble['body'])
        ->not->toContain('Example heading')
        ->not->toContain('Never indexed')
        ->not->toContain('welcome')
        ->not->toContain('`')
        ->not->toContain('~')
        ->and($trouble['headings'])->toBe(['Troubleshooting']);
});

it('builds href from prefix and slug', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($home['href'])->toBe('/docs')
        ->and($deploy['href'])->toBe('/docs/guides/deploy');
});

it('groups pages by their navigation group label, and root pages under none', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $records = new SearchIndexBuilder($vault)->build();
    $home = array_find($records, fn (array $r): bool => $r['slug'] === '/');
    $deploy = array_find($records, fn (array $r): bool => $r['slug'] === 'guides/deploy');

    expect($deploy['group'])->toBe('Guides')
        ->and($home['group'])->toBeNull();
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

it('changes signature when the index format changes', function () use ($fixturePath): void {
    $vault = makeSearchVault($fixturePath);

    $previous = new SearchIndexBuilder($vault, indexFormat: SearchIndexBuilder::INDEX_FORMAT - 1)->signature();
    $current = new SearchIndexBuilder($vault)->signature();

    expect($current)->not->toBe($previous);
});
