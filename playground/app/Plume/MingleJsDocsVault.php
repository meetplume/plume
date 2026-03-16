<?php

declare(strict_types=1);

namespace App\Plume;

use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Vault;

class MingleJsDocsVault extends Vault
{
    protected string $prefix = '/minglejs/docs';

    protected string $path = 'content/minglejs';

    protected string $layout = 'docs';

    /**
     * @return array<int, NavGroup>
     */
    public function navigation(): array
    {
        return [
            NavGroup::make('introduction')
                ->pages([
                    Page::make('what-is-minglejs')->label('What is MingleJS?'),
                    Page::make('why-minglejs')->label('Why MingleJS?'),
                    Page::make('getting-started')->label('Getting Started'),
                    Page::make('creating-mingles')->label('Creating Mingles'),
                ]),

            NavGroup::make('digging-deeper')
                ->pages([
                    Page::make('anatomy-of-a-mingle')->label('Anatomy of a Mingle'),
                    Page::make('anatomy-vue-specifics')->label('Anatomy - Vue specifics'),
                    Page::make('anatomy-react-specifics')->label('Anatomy - React specifics'),
                    Page::make('manual-instructions')->label('Manual Instructions'),
                    Page::make('events-and-backend')->label('Events and Backend'),
                    Page::make('filament-and-react')->label('Filament & React'),
                    Page::make('auto-import-on-build')->label('Auto Import on Build'),
                    Page::make('configuration')->label('Configuration'),
                ]),
        ];
    }
}
