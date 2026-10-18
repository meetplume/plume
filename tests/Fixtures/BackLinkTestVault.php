<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Http\Request;

class BackLinkTestVault extends SearchTestVault
{
    protected string $prefix = '/help';

    public function backLink(Request $request): array
    {
        return ['label' => 'Back to the app', 'href' => '/dashboard'];
    }
}
