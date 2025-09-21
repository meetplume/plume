<?php

use App\Enums\SiteSettings;
use App\Support\Nav\NavMenu;
use App\Support\Nav\NavMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Rawilk\Settings\Facades\Settings;

uses(RefreshDatabase::class)->group('nav-menu');

test('can process regular menu items', function () {
    // Set up test menu items
    $menuItems = [
        [
            'icon' => 'o-home',
            'name' => 'Home',
            'url' => '/',
            'open_in_new_tab' => false,
            'page' => 'home',
        ],
        [
            'icon' => 'o-newspaper',
            'name' => 'Blog',
            'url' => '/blog',
            'open_in_new_tab' => false,
            'page' => 'blog',
        ],
    ];

    // Store original value
    $originalValue = SiteSettings::MAIN_MENU->get();

    // Set test data
    Settings::set(SiteSettings::MAIN_MENU->value, $menuItems);

    $processedItems = NavMenu::getMainMenuItems();

    expect($processedItems)->toHaveCount(2)
        ->and($processedItems[ 0 ])->toBeInstanceOf(NavMenuItem::class)
        ->and($processedItems[ 0 ]->component)->toBeNull()
        ->and($processedItems[ 0 ]->name)->toBe('Home');

    // Restore original value
    Settings::set(SiteSettings::MAIN_MENU->value, $originalValue);
});

test('can process user account component menu item', function () {
    // Set up test menu with user-account item
    $menuItems = [
        [
            'icon' => 'o-home',
            'name' => 'Home',
            'url' => '/',
            'open_in_new_tab' => false,
            'page' => 'home',
        ],
        [
            'icon' => 'o-user',
            'name' => 'Account',
            'url' => '#',
            'open_in_new_tab' => false,
            'page' => 'user-account',
        ],
    ];

    // Mock the Settings facade
    Settings::shouldReceive('context')
        ->andReturnSelf()
        ->shouldReceive('get')
        ->with('main_menu', \Mockery::any())
        ->andReturn($menuItems);

    $processedItems = NavMenu::getMainMenuItems();

    expect($processedItems)->toHaveCount(2)
        ->and($processedItems[ 0 ]->component)->toBeNull()
        ->and($processedItems[ 0 ]->name)->toBe('Home')
        ->and($processedItems[ 1 ]->component)->toBe('nav.account-dropdown')
        ->and($processedItems[ 1 ]->nameForAnalytics)->toBe('account')
        ->and($processedItems[ 1 ]->name)->toBe('')
        ->and($processedItems[ 1 ]->url)->toBe('');
});

test('can handle mixed regular and component menu items', function () {
    $menuItems = [
        [
            'icon' => 'o-home',
            'name' => 'Home',
            'url' => '/',
            'open_in_new_tab' => false,
            'page' => 'home',
        ],
        [
            'icon' => 'o-user',
            'name' => 'Account',
            'url' => '#',
            'open_in_new_tab' => false,
            'page' => 'user-account',
        ],
        [
            'icon' => 'o-newspaper',
            'name' => 'Blog',
            'url' => '/blog',
            'open_in_new_tab' => false,
            'page' => 'blog',
        ],
    ];

    // Mock the Settings facade
    Settings::shouldReceive('context')
        ->andReturnSelf()
        ->shouldReceive('get')
        ->with('main_menu', \Mockery::any())
        ->andReturn($menuItems);

    $processedItems = NavMenu::getMainMenuItems();

    expect($processedItems)->toHaveCount(3)
        ->and($processedItems[ 0 ]->component)->toBeNull()
        ->and($processedItems[ 2 ]->component)->toBeNull()
        ->and($processedItems[ 1 ]->component)->toBe('nav.account-dropdown');
});
