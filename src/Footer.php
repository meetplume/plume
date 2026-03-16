<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class Footer
{
    private ?string $text = null;

    /** @var array<int, FooterColumn> */
    private array $columns = [];

    public function __construct(
        public readonly string $component,
    ) {}

    public static function make(string $component): self
    {
        return new self($component);
    }

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param  array<int, FooterColumn>  $columns
     */
    public function columns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array{type: string, text: string|null, columns: array<int, array{label: string, links: array<int, array{label: string, href: string}>}>}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->component,
            'text' => $this->text,
            'columns' => array_map(fn (FooterColumn $column): array => $column->toArray(), $this->columns),
        ];
    }
}
