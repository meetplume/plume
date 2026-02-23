<?php

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Facades\Plume;
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

Plume::page('/docs/build-interactive-menus', base_path('content/laravel-zero/build-interactive-menus.md'));
