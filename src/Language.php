<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class Language
{
    private bool $default = false;

    /** @var array<string, string> */
    private array $slugs = [];

    public function __construct(
        public readonly string $code,
        public readonly string $name,
    ) {}

    public static function make(string $code, string $name): self
    {
        return new self($code, $name);
    }

    public function default(bool $default = true): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @param  array<string, string>  $slugs  key => translated-slug
     */
    public function slugs(array $slugs): self
    {
        $this->slugs = $slugs;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @return array<string, string>
     */
    public function getSlugs(): array
    {
        return $this->slugs;
    }

    public function resolveSlug(string $pageKey): string
    {
        return $this->slugs[$pageKey] ?? $pageKey;
    }
}
