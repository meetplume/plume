<?php

use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;

it('derives label from key by default', function (): void {
    $group = NavGroup::make('getting-started');

    expect($group->getLabel())->toBe('Getting Started');
});

it('stores pages', function (): void {
    $page1 = Page::make('intro');
    $page2 = Page::make('setup');

    $group = NavGroup::make('basics')->pages([$page1, $page2]);

    expect($group->getPages())->toHaveCount(2)
        ->and($group->getPages()[0]->key)->toBe('intro')
        ->and($group->getPages()[1]->key)->toBe('setup');
});
