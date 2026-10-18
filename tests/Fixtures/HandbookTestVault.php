<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\PageAction;
use Meetplume\Plume\Language;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Vault;

/**
 * A vault with languages and an absolute route, that keeps its page menu on the site.
 */
class HandbookTestVault extends Vault
{
    protected string $prefix = '/handbook';

    protected string $path = '';

    protected string $layout = 'docs';

    public function __construct()
    {
        $this->path = __DIR__.'/../Unit/fixtures/handbook';
    }

    public function navigation(): array
    {
        return [
            NavGroup::make('handbook')->pages([
                Page::make('index')->home(),
                Page::make('guides/onboarding'),
            ]),
        ];
    }

    public function pages(): array
    {
        return [
            Page::make('about')->route('/about'),
        ];
    }

    public function languages(): array
    {
        return [
            Language::make('en', 'English')->default(),
            Language::make('fr', 'Français'),
        ];
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }

    public function pageActions(): array
    {
        return PageAction::internal();
    }
}
