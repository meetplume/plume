<?php

namespace App\Enums;

use Phiki\Theme\ParsedTheme;
use Phiki\Theme\ThemeParser;
use Phiki\Contracts\ThemeRepositoryInterface;
enum CodeTheme: string
{
    case Minimal = 'minimal';

    public function path(): string
    {
        return match ($this) {
            default => resource_path("/code-themes/{$this->value}.json"),
        };
    }

    /**
     * @throws \Phiki\Exceptions\UnrecognisedThemeException
     */
    public function toParsedTheme(ThemeRepositoryInterface $repository): ParsedTheme
    {
        return $repository->get($this->value);
    }

    public static function parse(array $theme): ParsedTheme
    {
        return (new ThemeParser)->parse($theme);
    }
}

