<?php

namespace App\Support\Footer;

readonly class FooterMenuItem
{
    public function __construct(
        public string $name,
        public string $url,
        public bool $open_in_new_tab = false
    ) {
    }

    public static function make(string $name, string $url, bool $openInNewTab = false): self
    {
        return new self($name, $url, $openInNewTab);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'open_in_new_tab' => $this->open_in_new_tab,
        ];
    }
}
