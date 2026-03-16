<?php

use Meetplume\Plume\FilesystemScanner;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;

$fixturePath = __DIR__.'/fixtures/scanner';

it('scans all md files recursively', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $slugs = $scanner->scanSlugs();

    expect($slugs)->toContain('/', 'config')
        ->and(count($slugs))->toBe(4);
});

it('creates page objects from md files', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $pages = $scanner->scanPages();

    expect($pages)->toHaveCount(4);

    $keys = array_map(fn (Page $p): string => $p->key, $pages);
    expect($keys)->toContain('index', 'getting-started/introduction', 'getting-started/installation', 'advanced/configuration');
});

it('treats index.md at root as root slug', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $pages = $scanner->scanPages();
    $index = array_find($pages, fn ($p): bool => $p->key === 'index');

    expect($index)->not->toBeNull()
        ->and($index->getSlug())->toBe('/');
});

it('respects frontmatter slug override', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $pages = $scanner->scanPages();
    $config = array_find($pages, fn ($p): bool => $p->key === 'advanced/configuration');

    expect($config)->not->toBeNull()
        ->and($config->getSlug())->toBe('config');
});

it('applies frontmatter title as label', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $pages = $scanner->scanPages();
    $intro = array_find($pages, fn ($p): bool => $p->key === 'getting-started/introduction');

    expect($intro)->not->toBeNull()
        ->and($intro->getLabel())->toBe('Intro');
});

it('applies frontmatter order', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $pages = $scanner->scanPages();
    $intro = array_find($pages, fn ($p): bool => $p->key === 'getting-started/introduction');
    $install = array_find($pages, fn ($p): bool => $p->key === 'getting-started/installation');

    expect($intro->getOrder())->toBe(1)
        ->and($install->getOrder())->toBe(2);
});

it('sets file path relative to base', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $pages = $scanner->scanPages();
    $intro = array_find($pages, fn ($p): bool => $p->key === 'getting-started/introduction');

    expect($intro->getPath())->toBe('getting-started/introduction.md');
});

it('builds nav groups from directories', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $groups = $scanner->scanNavigation();

    $keys = array_map(fn (NavGroup $g): string => $g->key, $groups);

    expect($keys)->toContain('_root', 'getting-started', 'advanced');
});

it('sorts pages within groups by order then key', function () use ($fixturePath): void {
    $scanner = new FilesystemScanner($fixturePath);

    $groups = $scanner->scanNavigation();
    $gettingStarted = array_find($groups, fn ($g): bool => $g->key === 'getting-started');

    $pageSlugs = array_map(fn (Page $p): string => $p->key, $gettingStarted->getPages());

    expect($pageSlugs)->toBe(['getting-started/introduction', 'getting-started/installation']);
});

it('returns empty for non-existent directory', function (): void {
    $scanner = new FilesystemScanner('/non/existent/path');

    expect($scanner->scanPages())->toBe([])
        ->and($scanner->scanSlugs())->toBe([])
        ->and($scanner->scanNavigation())->toBe([]);
});

it('excludes files starting with underscore', function (): void {
    $tmpDir = sys_get_temp_dir().'/plume-scanner-test-'.uniqid();
    mkdir($tmpDir);
    file_put_contents($tmpDir.'/visible.md', '# Visible');
    file_put_contents($tmpDir.'/_hidden.md', '# Hidden');

    $scanner = new FilesystemScanner($tmpDir);
    $slugs = $scanner->scanSlugs();

    expect($slugs)->toBe(['visible'])
        ->and($slugs)->not->toContain('_hidden');

    unlink($tmpDir.'/visible.md');
    unlink($tmpDir.'/_hidden.md');
    rmdir($tmpDir);
});
