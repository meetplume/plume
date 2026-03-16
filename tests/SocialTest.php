<?php

use Meetplume\Plume\Social;

it('creates github social', function (): void {
    $social = Social::github('https://github.com/test');

    expect($social->icon)->toBe('github')
        ->and($social->url)->toBe('https://github.com/test');
});

it('creates custom social with make()', function (): void {
    $social = Social::make('mastodon', 'https://mastodon.social/@test');

    expect($social->icon)->toBe('mastodon')
        ->and($social->url)->toBe('https://mastodon.social/@test');
});

it('serializes to array', function (): void {
    $social = Social::github('https://github.com/test');

    expect($social->toArray())->toBe([
        'icon' => 'github',
        'url' => 'https://github.com/test',
    ]);
});

it('creates all social platform variants', function (): void {
    expect(Social::x('https://x.com/test')->icon)->toBe('x')
        ->and(Social::discord('https://discord.gg/test')->icon)->toBe('discord')
        ->and(Social::youtube('https://youtube.com/@test')->icon)->toBe('youtube')
        ->and(Social::bluesky('https://bsky.app/test')->icon)->toBe('bluesky');
});
