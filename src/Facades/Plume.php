<?php

declare(strict_types=1);

namespace Meetplume\Plume\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Meetplume\Plume\Collection collection(string $prefix, string $contentPath)
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
