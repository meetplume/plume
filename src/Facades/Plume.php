<?php

declare(strict_types=1);

namespace Meetplume\Plume\Facades;

use Illuminate\Support\Facades\Facade;
use Meetplume\Plume\PlumeConfiguration;
use Meetplume\Plume\Vault;

/**
 * @method static PlumeConfiguration configure()
 * @method static ?PlumeConfiguration getConfiguration()
 * @method static ?Vault getVault(string $prefix)
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
