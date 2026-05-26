<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Vault;

class SearchTestVault extends Vault
{
    protected string $prefix = '/docs';

    protected string $path = '';

    protected string $layout = 'docs';

    protected Discovery $discovery = Discovery::Auto;

    public function __construct()
    {
        $this->path = dirname(__DIR__).'/Unit/fixtures/search';
    }

    public function canAccess(\Illuminate\Http\Request $request): bool
    {
        return true;
    }
}
