<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class FooterColumn
{
    /** @var array<int, array{label: string, href: string}> */
    private array $links = [];

    public function __construct(
        public readonly string $label,
    ) {}

    public static function make(string $label): self
    {
        return new self($label);
    }

    /**
     * @param  array<int, array{label: string, href: string}>  $links
     */
    public function links(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return array{label: string, links: array<int, array{label: string, href: string}>}
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'links' => $this->links,
        ];
    }
}
