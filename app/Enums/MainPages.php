<?php

namespace App\Enums;

enum MainPages: string
{
    case HOME = 'home';
    case BLOG = 'blog';
    case CATEGORIES = 'categories';
    case TAGS = 'tags';

    /**
     * Get the default url for each page
     */
    public function getDefaultSlug(): string
    {
        return match ($this) {
            self::HOME => '',
            self::BLOG => 'blog',
            self::CATEGORIES => 'categories',
            self::TAGS => 'tags',
        };
    }

    public function getTitle(): string
    {
        return match ($this) {
            self::HOME => __('Home'),
            self::BLOG => __('Blog'),
            self::CATEGORIES => __('Categories'),
            self::TAGS => __('Tags'),
        };
    }
}
