<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

/**
 * Laravel Boost drops a skill without a word when its frontmatter does not
 * parse, or lacks a name or a description.
 */
it('ships Boost skills that Boost can read', function (string $directory): void {
    $content = (string) file_get_contents($directory.'/SKILL.md');

    expect(preg_match('/^---\n(.*?)\n---\n/s', $content, $matches))->toBe(1);

    $frontmatter = Yaml::parse($matches[1]);

    expect($frontmatter['name'] ?? null)->toBe(basename($directory))
        ->and($frontmatter['description'] ?? null)->toBeString()->not->toBeEmpty();
})->with(fn (): array => glob(__DIR__.'/../../resources/boost/skills/*', GLOB_ONLYDIR) ?: []);
