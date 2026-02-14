<?php

use Meetplume\Plume\Frontmatter;

it('parses frontmatter from markdown content', function (): void {
    $content = <<<'MD'
        ---
        title: Hello World
        description: A test page
        ---

        # Hello World
        MD;

    $result = Frontmatter::parse($content);

    expect($result)->toBe([
        'title' => 'Hello World',
        'description' => 'A test page',
    ]);
});

it('returns empty array when no frontmatter is present', function (): void {
    $content = '# Hello World';

    $result = Frontmatter::parse($content);

    expect($result)->toBe([]);
});
