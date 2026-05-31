---
title: Sidebar Navigation
description: Build a sidebar with NavGroup and Page by overriding navigation().
---

# Sidebar Navigation

Define a vault's sidebar by overriding the `navigation()` method. It returns an array
of `NavGroup`s, each holding a list of `Page`s.

```php
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;

public function navigation(): array
{
    return [
        NavGroup::make('getting-started')
            ->icon('rocket')
            ->pages([
                Page::make('index')->label('Home')->home(),
                Page::make('installation')->label('Installation'),
                Page::make('configuration')->label('Configuration'),
            ]),

        NavGroup::make('guides')
            ->icon('book')
            ->pages([
                Page::make('deployment')->label('Deployment'),
            ]),
    ];
}
```

## NavGroup

A `NavGroup` is a labeled section of the sidebar.

```php
NavGroup::make('getting-started')   // key — used internally
    ->label('Getting Started')      // optional; defaults to a humanized key
    ->icon('rocket')                // optional; a Lucide icon name
    ->pages([ /* Page[] */ ]);
```

| Method      | Description                                                                |
|-------------|----------------------------------------------------------------------------|
| `make($key)`| Creates the group. The key is humanized into a label if none is set.       |
| `label()`   | The text shown in the sidebar. Defaults to the title-cased key.            |
| `icon()`    | A [Lucide](https://lucide.dev/icons) icon name (e.g. `rocket`, `book-open`). |
| `pages()`   | The pages in this group.                                                    |

::: tip
Icon names are matched flexibly — `book-open`, `bookOpen` and `BookOpen` all resolve to
the same Lucide icon. Browse the full set at [lucide.dev/icons](https://lucide.dev/icons).
:::

## Page

Each `Page` points to a Markdown file. The `make()` argument is the file path
(without `.md`) relative to the vault's `path`.

```php
Page::make('installation')          // → content/<vault>/installation.md
    ->label('Installation')          // sidebar link text
    ->home();                         // mark as the landing page (slug '/')
```

`Page` has many more options — slugs, ordering, per-page layouts, hidden pages and
absolute routes. See the dedicated [Pages](/docs/navigation/pages) page for the full
reference.

## Nested file paths

The page key may include subdirectories, mirroring how your files are organized on disk:

```php
NavGroup::make('getting-started')->pages([
    Page::make('getting-started/installation')->label('Installation'),
    Page::make('guides/deployment')->label('Deployment'),
]);
```

Here `getting-started/installation` resolves to
`content/<vault>/getting-started/installation.md`.

## Prev / next & table of contents

When using the `docs` layout, the sidebar order also drives the **previous / next**
links at the bottom of each page, and a **table of contents** is generated from the
page's headings automatically. No extra configuration needed.
