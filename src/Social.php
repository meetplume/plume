<?php

declare(strict_types=1);

namespace Meetplume\Plume;

final readonly class Social
{
    public function __construct(
        public string $icon,
        public string $url,
    ) {}

    public static function make(string $icon, string $url): self
    {
        return new self($icon, $url);
    }

    public static function github(string $url): self
    {
        return new self('github', $url);
    }

    public static function x(string $url): self
    {
        return new self('x', $url);
    }

    public static function discord(string $url): self
    {
        return new self('discord', $url);
    }

    public static function youtube(string $url): self
    {
        return new self('youtube', $url);
    }

    public static function bluesky(string $url): self
    {
        return new self('bluesky', $url);
    }

    /**
     * @return array{icon: string, url: string}
     */
    public function toArray(): array
    {
        return [
            'icon' => $this->icon,
            'url' => $this->url,
        ];
    }
}
