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
