<?php

use App\Enums\MainPages;

test('main front pages urls have no smoke', function () {
    $urls = collect(MainPages::cases())
        ->map(fn (MainPages $page) => url('/' . $page->getDefaultSlug()))
        ->toArray();

    $pages = visit($urls);
    $pages->assertNoSmoke();
});
