<?php

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Facades\Plume;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/movies', function () {
    return Page::render('page1', [
        'title' => 'Iconic Movies',
        'message' => 'This displays a page displaying iconic movies. It is rendered using the Page class from the Plume package.',
    ]);
});

Route::get('/tv-shows', function () {
    return Page::render('page2', [
        'title' => 'Documentation',
        'message' => 'This displays a page with TV shows. It is rendered using the Page class from the Plume package.',
    ]);
});

Route::get('/laravel-zero', function () {
    return Page::render('laravel-zero', (array) require base_path('page-sources/index-array.php'));
});

Route::get('/laravel-zero-md', function () {
    return Page::render('laravel-zero', base_path('page-sources/index-frontmatter.md'));
});

Plume::collection('plume/docs', base_path('/../docs'))
    ->title('Plume')
    ->navigation([
        NavGroup::make('getting-started')
            ->icon('rocket')
            ->pages([
                Page::make('home'),
                Page::make('index')->label('Introduction')->slug('introduction'),
                Page::make('installation'),
            ]),

        NavGroup::make('how-to-write')
            ->icon('notebook-pen')
            ->pages([
                Page::make('configuration')->label('Configuration'),
                Page::make('callouts'),
            ]),
    ]);

Plume::collection('/laravel-zero/docs', base_path('content/laravel-zero'))
    ->title('Laravel Zero')
    ->navigation([

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
                Page::make('upgrade')
                    ->label('Upgrade'),
                Page::make('contributing'),
            ]),
    ]);

Plume::collection('minglejs/docs', base_path('content/minglejs'))
    ->title('MingleJS')
    ->navigation([
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
    ]);
