<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content
    |--------------------------------------------------------------------------
    |
    | This file is for storing the settings for Content.
    | The path is relative to `/content` directory.
    |
    */

    'collections' => [
        'blog' => [
            'path' => 'blog',
//          'url' => 'blog',
//          'template' => 'prezet.show',
        ],
        'docs' => [
            'path' => 'docs',
//            'url' => 'docs',
//            'template' => 'prezet.show',
        ],
        'releases' => [
            'path' => 'releases',
//            'url' => 'releases',
//            'template' => 'prezet.show',
        ],
    ],

    'path' => base_path('content'),

];
