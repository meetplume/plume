<?php

declare(strict_types=1);

namespace Meetplume\Plume;

class Tab
{
    private ?string $label = null;

    private ?string $icon = null;

    /** @var array<int, NavGroup> */
    private array $groups = [];

    public function __construct(
        public readonly string $key,
    ) {}

    public static function make(string $key): static
    {
        return new static($key);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<int, NavGroup>  $groups
     */
    public function groups(array $groups): static
    {
        $this->groups = $groups;

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
     * @return array<int, NavGroup>
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
