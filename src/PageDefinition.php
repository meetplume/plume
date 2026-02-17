<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class PageDefinition
{
    public function __construct(
        public readonly string $filePath,
    ) {}

    /**
     * @return array{content: string, title: ?string, description: ?string, meta: array<string, mixed>}
     */
    public function toInertiaProps(): array
    {
        $rawContent = file_get_contents($this->filePath);
        $frontmatter = Frontmatter::parse($rawContent);

        return [
            'content' => $rawContent,
            'title' => $frontmatter['title'] ?? null,
            'description' => $frontmatter['description'] ?? null,
            'meta' => $frontmatter,
        ];
    }
}
