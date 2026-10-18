<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Symfony\Component\Yaml\Yaml;

class Frontmatter
{
    /**
     * @return array<string, mixed>
     */
    public static function parse(string $rawContent): array
    {
        if (! preg_match('/^---\r?\n(.*?)\r?\n---/s', $rawContent, $matches)) {
            return [];
        }

        $parsed = Yaml::parse($matches[1]);

        return is_array($parsed) ? $parsed : [];
    }

    /**
     * The content without its frontmatter, nor the blank lines that follow it.
     */
    public static function strip(string $rawContent): string
    {
        return ltrim((string) preg_replace('/^---\r?\n(?:.*?\r?\n)??---[ \t]*(?:\r?\n|$)/s', '', $rawContent, 1), "\r\n");
    }
}
