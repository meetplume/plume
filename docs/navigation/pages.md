---
title: Pages
description: Every method on the Page builder — labels, slugs, order, layout, routes and more.
---

# Pages

A `Page` maps a Markdown file to a route and a sidebar entry. Create one with
`Page::make($key)`, where `$key` is the file path (without `.md`) relative to the
vault's `path`.

```php
use Meetplume\Plume\Page;

Page::make('installation');   // → content/<vault>/installation.md, slug "installation"
```

## Builder methods

| Method            | Description                                                                 |
|-------------------|-----------------------------------------------------------------------------|
| `make($key)`      | Creates the page. The key is the default file path and slug.                |
| `label($text)`    | Sidebar link text. Falls back to frontmatter `title`, then a humanized key. |
| `slug($slug)`     | The URL slug. Defaults to the key.                                          |
| `home()`          | Shorthand for `slug('/')` — marks the vault's landing page.                 |
| `path($path)`     | Override the file path. Defaults to `"{key}.md"`.                           |
| `order($int)`     | Sort order within the group (lower comes first).                            |
| `layout($name)`   | Use a specific [layout](/docs/appearance/layouts) for this page only.       |
| `route($path)`    | Serve the page at an absolute route instead of under the vault prefix.      |
| `hidden()`        | Keep the page routable but remove it from the sidebar.                      |

## Labels

The label resolves in this order: explicit `->label()` → the file's frontmatter
`title` → a title-cased version of the key.

```php
Page::make('installation')->label('Install Plume');   // explicit
Page::make('installation');                             // uses frontmatter title, or "Installation"
```

## Slugs

By default the slug equals the key. Override it to decouple the URL from the filename:

```php
Page::make('getting-started/installation')
    ->slug('install');   // served at /docs/install
```

`->home()` is the common case for a landing page — it sets the slug to `/`:

```php
Page::make('index')->home();   // served at /docs
```

## Ordering

Within a group, pages sort by `order` (ascending). Pages without an order fall to the
bottom, sorted alphabetically by key.

```php
NavGroup::make('guides')->pages([
    Page::make('deployment')->order(1),
    Page::make('scaling')->order(2),
    Page::make('faq'),            // no order → sorted last
]);
```

## Per-page layout

A page can opt out of the vault's default layout:

```php
Page::make('changelog')->layout('changelog');
```

See [Layouts](/docs/appearance/layouts) for the available layouts.

## Hidden pages

A hidden page is fully routable but does not appear in the sidebar — handy for legal
pages, redirects, or deep-linked content:

```php
Page::make('legacy-notes')->hidden();
```

## Absolute routes & extra pages

Pages declared in `navigation()` live under the vault prefix. To serve a page at a
**different** URL — or to register a routable page that isn't in the sidebar — return
it from the vault's `pages()` method.

```php
public function pages(): array
{
    return [
        // A marketing landing page at the site root, using the "page" layout
        Page::make('homepage')
            ->route('/')
            ->layout('page')
            ->label('Home'),
    ];
}
```

`->route()` mounts the page at an absolute path, ignoring the vault prefix. This is how
a docs vault can also own a top-level landing page:

```php
class LaravelZeroDocsVault extends Vault
{
    protected string $prefix = '/laravel-zero/docs';

    protected ?string $home = '/laravel-zero';

    public function pages(): array
    {
        return [
            Page::make('laravel-zero-homepage')
                ->route('/laravel-zero')
                ->layout('page'),
        ];
    }
}
```
