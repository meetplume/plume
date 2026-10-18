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

    /**
     * The page's first `# ` heading, outside the frontmatter and code blocks:
     * the title of a file written without frontmatter.
     */
    public static function firstHeading(string $rawContent): ?string
    {
        $body = (string) preg_replace('/^---\r?\n.*?\r?\n---/s', '', $rawContent, 1);
        $body = (string) preg_replace('/^(`{3,}|~{3,}).*?^\1/ms', '', $body);

        if (preg_match('/^#\s+(.+?)\s*#*\s*$/m', $body, $matches) !== 1) {
            return null;
        }

        return trim(str_replace('`', '', $matches[1]));
    }
}
