<?php

use Meetplume\Plume\Page;

it('creates a page with make()', function (): void {
    $page = Page::make('intro');

    expect($page->key)->toBe('intro');
});

it('derives slug from key by default', function (): void {
    $page = Page::make('getting-started');

    expect($page->getSlug())->toBe('getting-started');
});

it('allows custom slug', function (): void {
    $page = Page::make('index')->slug('introduction');

    expect($page->getSlug())->toBe('introduction');
});

it('derives file path from key by default', function (): void {
    $page = Page::make('quickstart');

    expect($page->getPath())->toBe('quickstart.md');
});

it('allows custom file path', function (): void {
    $page = Page::make('quickstart')->path('guides/quickstart.md');

    expect($page->getPath())->toBe('guides/quickstart.md');
});

it('resolves label from explicit label', function (): void {
    $page = Page::make('intro')->label('Introduction');

    expect($page->resolveLabel())->toBe('Introduction');
});

it('resolves label from frontmatter title', function (): void {
    $page = Page::make('getting-started');

    $filePath = __DIR__.'/fixtures/pages/getting-started.md';
    expect($page->resolveLabel($filePath))->toBe('Getting Started');
});

it('resolves label from key when no label or frontmatter', function (): void {
    $page = Page::make('quick-start');

    expect($page->resolveLabel())->toBe('Quick Start');
});

it('builds inertia props from a markdown file', function (): void {
    $page = Page::make('getting-started');

    $filePath = __DIR__.'/fixtures/pages/getting-started.md';
    $props = $page->toInertiaProps($filePath);

    expect($props['title'])->toBe('Getting Started')
        ->and($props['description'])->toBe('Learn how to get started with Plume')
        ->and($props['content'])->toContain('# Getting Started');
});

it('takes the title and label of a file without frontmatter from its first heading', function (): void {
    $page = Page::make('no-frontmatter');

    $filePath = __DIR__.'/fixtures/pages/no-frontmatter.md';
    $props = $page->toInertiaProps($filePath);

    expect($props['title'])->toBe('Just a heading')
        ->and($page->resolveLabel($filePath))->toBe('Just a heading')
        ->and($props['description'])->toBeNull()
        ->and($props['content'])->toContain('No frontmatter here.');
});

it('skips headings inside code blocks when looking for the title', function (): void {
    $filePath = sys_get_temp_dir().'/plume-heading-'.uniqid().'.md';
    file_put_contents($filePath, "```bash\n# not a title\n```\n\n# Real title\n");

    expect(Page::make('heading')->resolveLabel($filePath))->toBe('Real title');

    unlink($filePath);
});

it('supports hidden flag', function (): void {
    $page = Page::make('draft')->hidden();

    expect($page->isHidden())->toBeTrue();
});

it('supports order', function (): void {
    $page = Page::make('intro')->order(1);

    expect($page->getOrder())->toBe(1);
});
