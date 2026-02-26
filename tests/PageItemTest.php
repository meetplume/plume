<?php

use Meetplume\Plume\Enums\CodeTheme;
use Meetplume\Plume\PageItem;

it('derives slug from key by default', function (): void {
    $page = new PageItem('getting-started');

    expect($page->getSlug())->toBe('getting-started');
});

it('derives file path from key by default', function (): void {
    $page = new PageItem('getting-started');

    expect($page->getPath())->toBe('getting-started.md');
});

it('builds inertia props from a markdown file', function (): void {
    $filePath = __DIR__.'/fixtures/pages/getting-started.md';
    $page = new PageItem('getting-started')
        ->codeTheme(CodeTheme::DRACULA, CodeTheme::DRACULA_SOFT);

    $props = $page->toInertiaProps($filePath);

    expect($props)
        ->toHaveKeys(['content', 'title', 'description', 'meta', 'codeThemeLight', 'codeThemeDark'])
        ->and($props['title'])->toBe('Getting Started')
        ->and($props['description'])->toBe('Learn how to get started with Plume')
        ->and($props['codeThemeLight'])->toBe('dracula')
        ->and($props['codeThemeDark'])->toBe('dracula-soft')
        ->and($props['content'])->toContain('# Getting Started');
});

it('builds inertia props from file without frontmatter', function (): void {
    $filePath = __DIR__.'/fixtures/pages/no-frontmatter.md';
    $page = new PageItem('no-frontmatter');

    $props = $page->toInertiaProps($filePath);

    expect($props['title'])->toBeNull()
        ->and($props['description'])->toBeNull()
        ->and($props['content'])->toContain('# Just a heading');
});
