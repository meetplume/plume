<?php

namespace App\Support;

class CommentMarkdownOptions
{
    public static function get(): array
    {
        return [
            'external_link' => [
                'internal_hosts' => config('app.url'),
                'open_in_new_window' => true,
                'html_class' => 'external-link',
                'nofollow' => '',
                'noopener' => 'external',
                'noreferrer' => 'external',
            ],
        ];
    }
}
