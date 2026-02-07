<?php

use Illuminate\Support\Facades\Route;
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
