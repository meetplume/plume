<?php

declare(strict_types=1);

use Meetplume\Plume\Frontmatter;

it('strips the frontmatter and the blank lines after it', function (string $content, string $expected): void {
    expect(Frontmatter::strip($content))->toBe($expected);
})->with([
    'frontmatter' => ["---\ntitle: Hello\n---\n\n# Hello\n", "# Hello\n"],
    'windows line endings' => ["---\r\ntitle: Hello\r\n---\r\n\r\n# Hello\r\n", "# Hello\r\n"],
    'empty frontmatter' => ["---\n---\n# Hello\n\n---\n\nBye\n", "# Hello\n\n---\n\nBye\n"],
    'no frontmatter' => ["# Hello\n\n---\n\nBye\n", "# Hello\n\n---\n\nBye\n"],
    'thematic break after the frontmatter' => ["---\ntitle: Hello\n---\n# Hello\n\n---\n\nBye\n", "# Hello\n\n---\n\nBye\n"],
    'only a frontmatter' => ["---\ntitle: Hello\n---", ''],
]);
