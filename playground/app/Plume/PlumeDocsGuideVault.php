<?php

declare(strict_types=1);

namespace App\Plume;

use Illuminate\Http\Request;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Vault;

class PlumeDocsGuideVault extends Vault
{
    protected string $prefix = '/docs';

    protected string $path = 'vendor/meetplume/plume/docs-guide';

    protected string $layout = 'docs';

    /**
     * @return array<int, NavGroup>
     */
    public function navigation(): array
    {
        return [
            NavGroup::make('getting-started')
                ->icon('rocket')
                ->pages([
                    Page::make('index')->label('Home')->home(),
                    Page::make('getting-started/installation')->label('Installation'),
                    Page::make('getting-started/configuration')->label('Configuration'),
                    Page::make('usage/navigation')->label('Navigation'),
                ]),
        ];
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
