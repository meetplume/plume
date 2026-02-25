<?php

use Meetplume\Plume\Collection;
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\PageItem;

function makeCollection(string $prefix = 'docs', ?string $contentPath = null): Collection
{
    return new Collection($prefix, $contentPath ?? __DIR__.'/fixtures/pages');
}

/**
 * Set navigation items on a collection without triggering route registration.
 *
 * @param  array<int, NavGroup|PageItem>  $items
 */
function setNavigation(Collection $collection, array $items): Collection
{
    $ref = new ReflectionClass($collection);

    $navProp = $ref->getProperty('navigation');
    $navProp->setValue($collection, $items);

    // Also resolve pages into the internal map (mirrors Collection::resolvePages)
    $resolvedPages = [];
    foreach ($items as $item) {
        if ($item instanceof NavGroup) {
            foreach ($item->getPages() as $page) {
                $resolvedPages[$page->getSlug()] = $page;
            }
        } elseif ($item instanceof PageItem) {
            $resolvedPages[$item->getSlug()] = $item;
        }
    }

    $resolvedProp = $ref->getProperty('resolvedPages');
    $resolvedProp->setValue($collection, $resolvedPages);

    return $collection;
}

it('resolves file path for a page', function (): void {
    $collection = makeCollection('docs', '/content/docs');
    $page = new PageItem('intro');

    expect($collection->resolveFilePath($page))->toBe('/content/docs/intro.md');
});

it('resolves file path with custom page path', function (): void {
    $collection = makeCollection('docs', '/content/docs');
    $page = new PageItem('intro')->path('guides/getting-started.md');

    expect($collection->resolveFilePath($page))->toBe('/content/docs/guides/getting-started.md');
});

it('resolves file path trimming trailing slash from content path', function (): void {
    $collection = makeCollection('docs', '/content/docs/');
    $page = new PageItem('intro');

    expect($collection->resolveFilePath($page))->toBe('/content/docs/intro.md');
});

it('resolves page label from explicit label', function (): void {
    $collection = makeCollection();
    $page = new PageItem('getting-started')->label('Get Started');

    expect($collection->resolvePageLabel($page))->toBe('Get Started');
});

it('resolves page label from frontmatter title', function (): void {
    $collection = makeCollection();
    $page = new PageItem('getting-started');

    expect($collection->resolvePageLabel($page))->toBe('Getting Started');
});

it('resolves page label from key when no label or frontmatter', function (): void {
    $collection = makeCollection();
    $page = new PageItem('no-frontmatter');

    expect($collection->resolvePageLabel($page))->toBe('No Frontmatter');
});

it('resolves page label from key when file does not exist', function (): void {
    $collection = makeCollection('docs', '/nonexistent/path');
    $page = new PageItem('some-page');

    expect($collection->resolvePageLabel($page))->toBe('Some Page');
});

it('builds navigation array with standalone pages', function (): void {
    $collection = makeCollection();
    setNavigation($collection, [
        new PageItem('getting-started')->label('Get Started'),
        new PageItem('no-frontmatter')->hidden(),
    ]);

    $nav = $collection->toNavigationArray();

    expect($nav)->toHaveCount(2)
        ->and($nav[0])->toMatchArray([
            'type' => 'page',
            'key' => 'getting-started',
            'label' => 'Get Started',
            'slug' => 'getting-started',
            'href' => '/docs/getting-started',
            'hidden' => false,
            'active' => false,
        ])
        ->and($nav[1])->toMatchArray([
            'type' => 'page',
            'hidden' => true,
        ]);
});

it('builds navigation array with groups', function (): void {
    $collection = makeCollection();
    setNavigation($collection, [
        NavGroup::make('guides')->label('Guides')->icon('book')->pages([
            new PageItem('getting-started')->label('Get Started'),
        ]),
    ]);

    $nav = $collection->toNavigationArray();

    expect($nav)->toHaveCount(1)
        ->and($nav[0])->toMatchArray([
            'type' => 'group',
            'key' => 'guides',
            'label' => 'Guides',
            'icon' => 'book',
        ])
        ->and($nav[0]['pages'] ?? null)->toHaveCount(1)
        ->and($nav[0]['pages'][0] ?? null)->toMatchArray([
            'type' => 'page',
            'key' => 'getting-started',
            'label' => 'Get Started',
            'slug' => 'getting-started',
            'href' => '/docs/getting-started',
        ]);
});

it('marks the active page in navigation array', function (): void {
    $collection = makeCollection();
    setNavigation($collection, [
        new PageItem('getting-started')->label('Get Started'),
        new PageItem('no-frontmatter')->label('Other'),
    ]);

    $nav = $collection->toNavigationArray('getting-started');

    expect($nav[0]['active'] ?? null)->toBeTrue()
        ->and($nav[1]['active'] ?? null)->toBeFalse();
});

it('marks the active page inside a group', function (): void {
    $collection = makeCollection();
    setNavigation($collection, [
        NavGroup::make('guides')->pages([
            new PageItem('getting-started')->label('Get Started'),
            new PageItem('no-frontmatter')->label('Other'),
        ]),
    ]);

    $nav = $collection->toNavigationArray('getting-started');

    expect($nav[0]['pages'][0]['active'] ?? null)->toBeTrue()
        ->and($nav[0]['pages'][1]['active'] ?? null)->toBeFalse();
});

it('uses custom slug in href and active matching', function (): void {
    $collection = makeCollection();
    setNavigation($collection, [
        new PageItem('getting-started')->slug('intro')->label('Intro'),
    ]);

    $nav = $collection->toNavigationArray('intro');

    expect($nav[0]['href'] ?? null)->toBe('/docs/intro')
        ->and($nav[0]['active'] ?? null)->toBeTrue();
});

it('handles mixed groups and standalone pages', function (): void {
    $collection = makeCollection();
    setNavigation($collection, [
        new PageItem('getting-started')->label('Home'),
        NavGroup::make('guides')->pages([
            new PageItem('no-frontmatter')->label('Guide'),
        ]),
    ]);

    $nav = $collection->toNavigationArray();

    expect($nav)->toHaveCount(2)
        ->and($nav[0]['type'])->toBe('page')
        ->and($nav[1]['type'])->toBe('group');
});
