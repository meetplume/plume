<?php

declare(strict_types=1);

namespace Tests;

use Meetplume\Plume\PlumeServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [PlumeServiceProvider::class];
    }
}
