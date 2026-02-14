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
}
