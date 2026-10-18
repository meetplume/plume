<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class GuardedTestVault extends SearchTestVault
{
    protected string $prefix = '/guarded';

    /**
     * @return array<int, string>
     */
    public function middleware(): array
    {
        return ['web', TeapotMiddleware::class];
    }
}
