<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class NavGroup
{
    private ?string $label = null;

    private ?string $icon = null;

    /** @var array<int, PageItem> */
    private array $pages = [];

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

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<int, PageItem>  $pages
     */
    public function pages(array $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? str($this->key)->replace('-', ' ')->title()->toString();
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return array<int, PageItem>
     */
    public function getPages(): array
    {
        return $this->pages;
    }
}
