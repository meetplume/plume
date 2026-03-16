<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class Version
{
    private bool $default = false;

    /** @var array<int, Tab>|null */
    private ?array $tabs = null;

    public function __construct(
        public readonly string $key,
    ) {}

    public static function make(string $key): self
    {
        return new self($key);
    }

    public function default(bool $default = true): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @param  array<int, Tab>  $tabs
     */
    public function tabs(array $tabs): self
    {
        $this->tabs = $tabs;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function hasTabs(): bool
    {
        return $this->tabs !== null;
    }

    /**
     * @return array<int, Tab>|null
     */
    public function getTabs(): ?array
    {
        return $this->tabs;
    }
}
