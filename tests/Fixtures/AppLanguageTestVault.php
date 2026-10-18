<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Language;
use Meetplume\Plume\Vault;

class AppLanguageTestVault extends Vault
{
    protected string $prefix = '/app-language';

    protected string $path = __DIR__.'/../Unit/fixtures/localized';

    protected string $layout = 'docs';

    protected Discovery $discovery = Discovery::Auto;

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

    public function languageFor(Request $request): ?string
    {
        return app()->getLocale();
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
