<?php

declare(strict_types=1);

namespace App\Plume;

use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Vault;

class LaravelZeroDocsVault extends Vault
{
    protected string $prefix = '/laravel-zero/docs';

    protected string $path = 'content/laravel-zero';

    protected string $layout = 'docs';

    /**
     * @return array<int, NavGroup>
     */
    public function navigation(): array
    {
        return [
            NavGroup::make('getting-started')
                ->pages([
                    Page::make('introduction'),
                    Page::make('installation'),
                    Page::make('commands'),
                    Page::make('service-providers'),
                    Page::make('configuration'),
                    Page::make('testing'),
                ]),

            NavGroup::make('add-ons')
                ->pages([
                    Page::make('database'),
                    Page::make('logging'),
                    Page::make('filesystem'),
                    Page::make('run-tasks'),
                    Page::make('task-scheduling'),
                    Page::make('build-interactive-menus'),
                    Page::make('send-desktop-notifications'),
                    Page::make('web-browser-automation'),
                    Page::make('environment-variables'),
                    Page::make('tinker-repl'),
                    Page::make('http-client'),
                    Page::make('view'),
                    Page::make('cache'),
                ]),

            NavGroup::make('distribute-your-app')
                ->label('Distribute your app')
                ->pages([
                    Page::make('distribute-as-a-phar-archive')
                        ->label('As a PHAR archive'),
                    Page::make('distribute-as-a-single-executable-binary')
                        ->label('As a single executable binary'),
                ]),

            NavGroup::make('more')
                ->pages([
                    Page::make('upgrade'),
                    Page::make('contributing'),
                ]),
        ];
    }
}
