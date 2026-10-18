<?php

declare(strict_types=1);

use Meetplume\Plume\VaultRouter;

it('puts the Markdown of a page at its address followed by .md', function (string $uri, string $expected): void {
    expect(VaultRouter::markdownUri($uri))->toBe($expected);
})->with([
    'page' => ['docs/guides/deploy', 'docs/guides/deploy.md'],
    'leading and trailing slashes' => ['/docs/', 'docs.md'],
    'site root' => ['/', 'index.md'],
]);
