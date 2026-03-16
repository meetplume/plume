<?php

use Meetplume\Plume\Footer;
use Meetplume\Plume\FooterColumn;

it('creates a footer with make()', function (): void {
    $footer = Footer::make('footer1');

    expect($footer->component)->toBe('footer1');
});

it('stores text', function (): void {
    $footer = Footer::make('footer1')->text('Built with Plume');

    expect($footer->toArray()['text'])->toBe('Built with Plume');
});

it('stores columns', function (): void {
    $footer = Footer::make('footer1')->columns([
        FooterColumn::make('Product')->links([
            ['label' => 'Features', 'href' => '/features'],
        ]),
    ]);

    $array = $footer->toArray();

    expect($array['columns'])->toHaveCount(1)
        ->and($array['columns'][0]['label'])->toBe('Product');
});

it('serializes to array', function (): void {
    $footer = Footer::make('footer1')->text('Test');

    $array = $footer->toArray();

    expect($array)->toHaveKeys(['type', 'text', 'columns'])
        ->and($array['type'])->toBe('footer1');
});
