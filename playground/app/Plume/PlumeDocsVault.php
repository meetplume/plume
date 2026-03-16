<?php

declare(strict_types=1);

namespace App\Plume;

use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Vault;

class PlumeDocsVault extends Vault
{
    protected string $prefix = '/plume/docs';

    protected string $path = 'vendor/meetplume/plume/docs';

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
                    Page::make('index'),
                    Page::make('home'),
                    Page::make('installation'),
                ]),

            NavGroup::make('how-to-write')
                ->icon('notebook-pen')
                ->pages([
                    Page::make('configuration'),
                    Page::make('callouts'),
                ]),
        ];
    }
}
