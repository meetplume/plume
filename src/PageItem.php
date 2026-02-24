<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class PageItem
{
    private ?string $label = null;

    private ?string $slug = null;

    private ?string $path = null;

    private ?int $order = null;

    private bool $hidden = false;

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
}
