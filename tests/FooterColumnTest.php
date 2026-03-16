<?php

use Meetplume\Plume\FooterColumn;

it('creates a column with make()', function (): void {
    $column = FooterColumn::make('Product');

    expect($column->label)->toBe('Product');
});

it('stores links', function (): void {
    $column = FooterColumn::make('Product')->links([
        ['label' => 'Features', 'href' => '/features'],
        ['label' => 'Pricing', 'href' => '/pricing'],
    ]);

    $array = $column->toArray();

    expect($array['links'])->toHaveCount(2)
        ->and($array['links'][0]['label'])->toBe('Features');
});

it('serializes to array', function (): void {
    $column = FooterColumn::make('Product')->links([
        ['label' => 'Features', 'href' => '/features'],
    ]);

    expect($column->toArray())->toBe([
        'label' => 'Product',
        'links' => [
            ['label' => 'Features', 'href' => '/features'],
        ],
    ]);
});
