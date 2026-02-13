<?php

use Meetplume\Plume\Page;
use Meetplume\Plume\Enums\Type;
use Meetplume\Plume\Facades\Plume;
use Meetplume\Plume\Enums\CodeTheme;
use Illuminate\Support\Facades\Route;

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

Plume::collection('/docs', base_path('content/docs'))
    ->type(Type::Documentation)
    ->codeTheme(
        light: CodeTheme::CATPPUCCIN_LATTE,
        dark: CodeTheme::CATPPUCCIN_MACCHIATO,
    );
