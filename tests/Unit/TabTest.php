<?php

use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Tab;

it('creates a tab with make()', function (): void {
    $tab = Tab::make('documentation');

    expect($tab->key)->toBe('documentation');
});

it('derives label from key', function (): void {
    $tab = Tab::make('api-reference');

    expect($tab->getLabel())->toBe('Api Reference');
});

it('allows custom label', function (): void {
    $tab = Tab::make('api')->label('API Reference');

    expect($tab->getLabel())->toBe('API Reference');
});

it('stores groups', function (): void {
    $tab = Tab::make('docs')->groups([
        NavGroup::make('getting-started')->pages([
            Page::make('intro'),
        ]),
    ]);

    expect($tab->getGroups())->toHaveCount(1)
        ->and($tab->getGroups()[0]->key)->toBe('getting-started');
});

it('returns icon', function (): void {
    $tab = Tab::make('docs')->icon('book');

    expect($tab->getIcon())->toBe('book');
});

it('returns null icon by default', function (): void {
    $tab = Tab::make('docs');

    expect($tab->getIcon())->toBeNull();
});
