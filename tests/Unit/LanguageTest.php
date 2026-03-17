<?php

use Meetplume\Plume\Language;

it('creates a language with make()', function (): void {
    $lang = Language::make('en', 'English');

    expect($lang->code)->toBe('en')
        ->and($lang->name)->toBe('English');
});

it('marks language as default', function (): void {
    $lang = Language::make('en', 'English')->default();

    expect($lang->isDefault())->toBeTrue();
});

it('is not default by default', function (): void {
    $lang = Language::make('pt', 'Português');

    expect($lang->isDefault())->toBeFalse();
});

it('stores slug translations', function (): void {
    $lang = Language::make('fr', 'Français')->slugs([
        'quickstart' => 'demarrage-rapide',
        'contact' => 'contactez-nous',
    ]);

    expect($lang->getSlugs())->toHaveCount(2);
});

it('resolves translated slug', function (): void {
    $lang = Language::make('fr', 'Français')->slugs([
        'quickstart' => 'demarrage-rapide',
    ]);

    expect($lang->resolveSlug('quickstart'))->toBe('demarrage-rapide');
});

it('falls back to original slug when no translation', function (): void {
    $lang = Language::make('fr', 'Français')->slugs([
        'quickstart' => 'demarrage-rapide',
    ]);

    expect($lang->resolveSlug('installation'))->toBe('installation');
});
