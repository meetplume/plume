<?php

declare(strict_types=1);

use Meetplume\Plume\Inertia\PlumeInertia;
use Meetplume\Plume\Plume;

it('registers Plume as singleton', function (): void {
    expect(app(Plume::class))->toBeInstanceOf(Plume::class)
        ->and(app(Plume::class))->toBe(app(Plume::class));
});

it('registers PlumeInertia as singleton', function (): void {
    expect(app(PlumeInertia::class))->toBeInstanceOf(PlumeInertia::class)
        ->and(app(PlumeInertia::class))->toBe(app(PlumeInertia::class));
});

it('registers plumeInertia blade directive', function (): void {
    $compiler = app('blade.compiler');
    $directives = $compiler->getCustomDirectives();

    expect($directives)->toHaveKey('plumeInertia')
        ->and($directives)->toHaveKey('plumeInertiaHead');
});
