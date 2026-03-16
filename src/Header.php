<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final class Header
{
    /** @var array<int, array{label: string, href: string}> */
    private array $links = [];

    /** @var array<int, Social> */
    private array $socials = [];

    private ?string $ctaLabel = null;

    private ?string $ctaHref = null;

    public function __construct(
        public readonly string $component,
    ) {}

    public static function make(string $component): self
    {
        return new self($component);
    }

    public function cta(string $label, string $href): self
    {
        $this->ctaLabel = $label;
        $this->ctaHref = $href;

        return $this;
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
     * @param  array<int, Social>  $socials
     */
    public function socials(array $socials): self
    {
        $this->socials = $socials;

        return $this;
    }

    /**
     * @return array{type: string, links: array<int, array{label: string, href: string}>, socials: array<int, array{icon: string, url: string}>, cta: array{label: string, href: string}|null}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->component,
            'links' => $this->links,
            'socials' => array_map(fn (Social $social): array => $social->toArray(), $this->socials),
            'cta' => $this->ctaLabel !== null && $this->ctaHref !== null
                ? ['label' => $this->ctaLabel, 'href' => $this->ctaHref]
                : null,
        ];
    }
}
