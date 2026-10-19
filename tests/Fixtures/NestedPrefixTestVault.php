<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Meetplume\Plume\Page;

/**
 * The handbook under a prefix of two segments, with an absolute route on a nested page.
 */
class NestedPrefixTestVault extends HandbookTestVault
{
    protected string $prefix = '/docs/handbook';

    public function pages(): array
    {
        return [
            Page::make('guides/onboarding')->route('/start'),
        ];
    }
}
