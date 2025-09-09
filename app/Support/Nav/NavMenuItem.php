<?php

namespace App\Support\Nav;

readonly class NavMenuItem
{
    public function __construct(
        public string $name,
        public string $url,
        public string $nameForAnalytics,
        public ?string $icon = null,
        public bool $open_in_new_tab = false,
        public bool $is_active = false
    ) {}

    public static function make(
        string $name,
        string $url,
        string $nameForAnalytics,
        ?string $icon = null,
        bool $openInNewTab = false,
        bool $isActive = false
    ): self {
        return new self($name, $url, $nameForAnalytics, $icon, $openInNewTab, $isActive);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'name_for_analytics' => $this->nameForAnalytics,
            'icon' => $this->icon,
            'open_in_new_tab' => $this->open_in_new_tab,
            'is_active' => $this->is_active,
        ];
    }
}
