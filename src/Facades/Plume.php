<?php

declare(strict_types=1);

namespace Meetplume\Plume\Facades;

use Illuminate\Support\Facades\Facade;
use Meetplume\Plume\Enums\CodeTheme;

/**
 * @method static void page(string $uri, string $filePath, CodeTheme $codeThemeLight = CodeTheme::GITHUB_LIGHT, CodeTheme $codeThemeDark = CodeTheme::GITHUB_DARK)
 *
 * @see \Meetplume\Plume\Plume
 */
class Plume extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Meetplume\Plume\Plume::class;
    }
}
