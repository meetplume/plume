<?php

declare(strict_types=1);

namespace Meetplume\Plume\Facades;

use Illuminate\Support\Facades\Facade;
use Meetplume\Plume\PageDefinition;

/**
 * @method static PageDefinition page(string $uri, string $filePath)
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
