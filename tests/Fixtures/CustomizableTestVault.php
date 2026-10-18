<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Vault;

class CustomizableTestVault extends Vault
{
    public static string $contentPath = '';

    protected string $prefix = '/custom';

    protected string $layout = 'docs';

    protected Discovery $discovery = Discovery::Auto;

    public function getPath(): string
    {
        return self::$contentPath;
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
