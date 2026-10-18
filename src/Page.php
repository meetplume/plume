<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class Page
{
    private ?string $label = null;

    private ?string $slug = null;

    private ?string $path = null;

    private ?int $order = null;

    private ?string $layout = null;

    private ?string $route = null;

    private bool $hidden = false;

    private ?string $language = null;

    public function __construct(
        public readonly string $key,
    ) {}

    public static function make(string $key): self
    {
        return new self($key);
    }

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

    public function home(): self
    {
        return $this->slug('/');
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

    public function layout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function route(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function hidden(bool $hidden = true): self
    {
        $this->hidden = $hidden;

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

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * The language whose folder holds the file, when it is not the language of
     * the request: a page served in the default language, not translated yet.
     */
    public function language(string $code): self
    {
        $this->language = $code;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Resolve label: explicit > frontmatter title > first `# ` heading > humanized key.
     */
    public function resolveLabel(?string $resolvedFilePath = null): string
    {
        if ($this->label !== null) {
            return $this->label;
        }

        if ($resolvedFilePath !== null && file_exists($resolvedFilePath)) {
            $rawContent = (string) file_get_contents($resolvedFilePath);
            $frontmatter = Frontmatter::parse($rawContent);

            if (isset($frontmatter['title']) && is_string($frontmatter['title'])) {
                return $frontmatter['title'];
            }

            $heading = Frontmatter::firstHeading($rawContent);

            if ($heading !== null) {
                return $heading;
            }
        }

        return str($this->key)->replace('-', ' ')->title()->toString();
    }

    /**
     * @return array{content: string, title: ?string, description: ?string, meta: array<string, mixed>, codeThemeLight: ?string, codeThemeDark: ?string}
     */
    public function toInertiaProps(?string $resolvedFilePath = null): array
    {
        $path = $resolvedFilePath;

        abort_unless($path !== null && file_exists($path), 404);

        $rawContent = (string) file_get_contents($path);
        $frontmatter = Frontmatter::parse($rawContent);

        return [
            'content' => $rawContent,
            'title' => $frontmatter['title'] ?? Frontmatter::firstHeading($rawContent),
            'description' => $frontmatter['description'] ?? null,
            'meta' => $frontmatter,
            'codeThemeLight' => null,
            'codeThemeDark' => null,
        ];
    }

    /**
     * The page's Markdown, without its frontmatter, as served at its `.md` address.
     */
    public function toMarkdown(?string $resolvedFilePath = null): string
    {
        abort_unless($resolvedFilePath !== null && file_exists($resolvedFilePath), 404);

        return Frontmatter::strip((string) file_get_contents($resolvedFilePath));
    }
}
