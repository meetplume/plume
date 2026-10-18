<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Language;
use Meetplume\Plume\Vault;

class LocalizedTestVault extends Vault
{
    protected string $prefix = '/localized';

    protected string $path = '';

    protected string $layout = 'docs';

    protected Discovery $discovery = Discovery::Auto;

    public function __construct()
    {
        $this->path = __DIR__.'/../Unit/fixtures/localized';
    }

    /**
     * @return array<int, Language>
     */
    public function languages(): array
    {
        return [
            Language::make('en', 'English')->default(),
            Language::make('pt', 'Português'),
        ];
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
