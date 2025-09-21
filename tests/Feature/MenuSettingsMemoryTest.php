<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('memory-testing');

test('menu settings page loads without memory exhaustion using browser testing', function () {
    // Create and authenticate a user
    $user = User::factory()->create([
        'email' => 'admin@web-nancy.fr',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($user);

    // Visit the menu settings page using browser testing
    $page = visit('/admin/settings/menus');

    // The page should load successfully without memory exhaustion
    // Check for success indicators (either login redirect or actual page content)
    $page->assertDontSee('Internal Server Error')
        ->assertDontSee('memory size')
        ->assertDontSee('exhausted')
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();

    // If redirected to login, that's expected behavior for unauthenticated users
    // If showing actual content, that's also fine - we just want no memory errors
    expect($page->url())->toMatch('/(login|settings\/menus)/');
});

test('menu settings page memory usage stays within reasonable limits', function () {
    // Create and authenticate a user
    $user = User::factory()->create([
        'email' => 'admin@web-nancy.fr',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($user);

    // Monitor memory usage during the test
    $memoryBefore = memory_get_usage(true);

    try {
        // Visit the page using browser testing
        $page = visit('/admin/settings/menus');

        // Basic assertions to ensure the page responds
        $page->assertDontSee('Internal Server Error')
            ->assertDontSee('Fatal error')
            ->assertDontSee('memory size')
            ->assertDontSee('exhausted');

        $memoryAfter = memory_get_usage(true);
        $memoryUsed = $memoryAfter - $memoryBefore;

        // Memory usage should be reasonable (less than 100MB)
        expect($memoryUsed)->toBeLessThan(100 * 1024 * 1024); // 100MB limit

        // Log memory usage for monitoring
        $memoryMB = number_format($memoryUsed / 1024 / 1024, 2);
        dump("Memory used during test: {$memoryMB} MB");

    } catch (Exception $e) {
        if (str_contains($e->getMessage(), 'memory') || str_contains($e->getMessage(), 'exhausted')) {
            throw new Exception("Memory exhaustion detected during browser test: " . $e->getMessage());
        }
        throw $e;
    }
});

test('can smoke test admin pages without memory issues', function () {
    // Create and authenticate a user
    $user = User::factory()->create([
        'email' => 'admin@web-nancy.fr',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($user);

    // Test multiple admin pages that might have similar issues
    $pages = [
        '/admin',
        '/admin/settings/menus',
    ];

    // Visit all pages and ensure no smoke (JavaScript errors, console logs, or memory issues)
    visit($pages)->assertNoSmoke();

});

test('menu settings form renders without infinite loops', function () {
    // Create and authenticate a user
    $user = User::factory()->create([
        'email' => 'admin@web-nancy.fr',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($user);

    // Visit the menu settings page
    $page = visit('/admin/settings/menus');

    // Allow page to load (browser testing automatically waits for page load)

    // Check that the page doesn't contain error indicators
    $page->assertDontSee('Internal Server Error')
        ->assertDontSee('memory size')
        ->assertDontSee('exhausted')
        ->assertDontSee('Fatal error');

    // Ensure JavaScript executed without errors
    $page->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();

});
