<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Meetplume\Plume\Enums\CodeTheme;

final class PageDefinition
{
    private CodeTheme $codeThemeLight = CodeTheme::GITHUB_LIGHT;

    private CodeTheme $codeThemeDark = CodeTheme::GITHUB_DARK;

    public function __construct(
        public readonly string $filePath,
    ) {}

    public function codeTheme(CodeTheme $light, CodeTheme $dark): self
    {
        $this->codeThemeLight = $light;
        $this->codeThemeDark = $dark;

        return $this;
    }

    /**
     * @return array{content: string, title: ?string, description: ?string, meta: array<string, mixed>, codeThemeLight: string, codeThemeDark: string}
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
            'codeThemeLight' => $this->codeThemeLight->value,
            'codeThemeDark' => $this->codeThemeDark->value,
        ];
    }
}
