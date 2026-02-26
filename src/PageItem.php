<?php

/** @noinspection PhpGetterAndSetterCanBeReplacedWithPropertyHooksInspection */

declare(strict_types=1);

namespace Meetplume\Plume;

use Meetplume\Plume\Enums\CodeTheme;

final class PageItem
{
    private ?string $label = null;

    private ?string $slug = null;

    private ?string $path = null;

    private ?int $order = null;

    private bool $hidden = false;

    private ?CodeTheme $codeThemeLight = null;

    private ?CodeTheme $codeThemeDark = null;

    private ?string $filePath = null;

    public function __construct(
        public readonly string $key,
    ) {}

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function order(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function hidden(bool $hidden = true): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function codeTheme(CodeTheme $light, CodeTheme $dark): self
    {
        $this->codeThemeLight = $light;
        $this->codeThemeDark = $dark;

        return $this;
    }

    public function filePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSlug(): string
    {
        return $this->slug ?? $this->key;
    }

    public function getPath(): string
    {
        return $this->path ?? $this->key.'.md';
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function getCodeThemeLight(): ?CodeTheme
    {
        return $this->codeThemeLight;
    }

    public function getCodeThemeDark(): ?CodeTheme
    {
        return $this->codeThemeDark;
    }

    /**
     * @return array{content: string, title: ?string, description: ?string, meta: array<string, mixed>, codeThemeLight: ?string, codeThemeDark: ?string}
     */
    public function toInertiaProps(?string $resolvedFilePath = null): array
    {
        $path = $resolvedFilePath ?? $this->filePath;

        abort_unless($path !== null && file_exists($path), 404);

        $rawContent = (string) file_get_contents($path);
        $frontmatter = Frontmatter::parse($rawContent);

        return [
            'content' => $rawContent,
            'title' => $frontmatter['title'] ?? null,
            'description' => $frontmatter['description'] ?? null,
            'meta' => $frontmatter,
            'codeThemeLight' => $this->codeThemeLight?->value,
            'codeThemeDark' => $this->codeThemeDark?->value,
        ];
    }
}
