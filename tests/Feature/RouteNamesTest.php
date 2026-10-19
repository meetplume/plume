<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Plume;
use Tests\Fixtures\NestedPrefixTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([NestedPrefixTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();
    Route::getRoutes()->refreshNameLookups();
});

it('names routes with dots where the prefix and page keys have slashes', function (): void {
    $names = collect(Route::getRoutes()->getRoutes())
        ->map(fn (Illuminate\Routing\Route $route): ?string => $route->getName())
        ->filter(fn (?string $name): bool => str_starts_with((string) $name, 'plume.'));

    expect($names)->not->toBeEmpty()
        ->and($names->filter(fn (string $name): bool => str_contains($name, '/')))->toBeEmpty()
        ->and(route('plume.docs.handbook.root', absolute: false))->toBe('/docs/handbook')
        ->and(route('plume.docs.handbook.route.guides.onboarding', absolute: false))->toBe('/start');
});
