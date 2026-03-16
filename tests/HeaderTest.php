<?php

use Meetplume\Plume\Header;
use Meetplume\Plume\Social;

it('creates a header with make()', function (): void {
    $header = Header::make('header1');

    expect($header->component)->toBe('header1');
});

it('stores cta', function (): void {
    $header = Header::make('header1')->cta('Get Started', '/docs');

    $array = $header->toArray();

    expect($array['cta'])->toBe(['label' => 'Get Started', 'href' => '/docs']);
});

it('stores links', function (): void {
    $header = Header::make('header1')->links([
        ['label' => 'Docs', 'href' => '/docs'],
        ['label' => 'Blog', 'href' => '/blog'],
    ]);

    $array = $header->toArray();

    expect($array['links'])->toHaveCount(2);
});

it('stores socials', function (): void {
    $header = Header::make('header1')->socials([
        Social::github('https://github.com/test'),
    ]);

    $array = $header->toArray();

    expect($array['socials'])->toHaveCount(1)
        ->and($array['socials'][0]['icon'])->toBe('github');
});

it('serializes to array', function (): void {
    $header = Header::make('header1')
        ->cta('Start', '/start')
        ->links([['label' => 'Docs', 'href' => '/docs']])
        ->socials([Social::github('https://github.com/test')]);

    $array = $header->toArray();

    expect($array)->toHaveKeys(['type', 'links', 'socials', 'cta'])
        ->and($array['type'])->toBe('header1');
});

it('returns null cta when not set', function (): void {
    $header = Header::make('header1');

    expect($header->toArray()['cta'])->toBeNull();
});
