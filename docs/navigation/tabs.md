---
title: Tabs
description: Split a vault into top-level tabs, each with its own navigation.
---

# Tabs

Tabs divide a single vault into top-level sections, each with its own sidebar. They're
ideal when one product has distinct areas — for example "Guides" and "API Reference" —
that share a prefix but shouldn't share a sidebar.

Override the `tabs()` method to return an array of `Tab`s:

```php
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;
use Meetplume\Plume\Tab;

public function tabs(): array
{
    return [
        Tab::make('guides')
            ->label('Guides')
            ->icon('book')
            ->groups([
                NavGroup::make('getting-started')->pages([
                    Page::make('index')->home(),
                    Page::make('installation'),
                ]),
            ]),

        Tab::make('api')
            ->label('API Reference')
            ->icon('code')
            ->groups([
                NavGroup::make('endpoints')->pages([
                    Page::make('api/users')->label('Users'),
                    Page::make('api/posts')->label('Posts'),
                ]),
            ]),
    ];
}
```

## Tab builder

| Method        | Description                                                       |
|---------------|-------------------------------------------------------------------|
| `make($key)`  | Creates the tab. The key appears in the URL.                      |
| `label()`     | Tab label. Defaults to the humanized key.                         |
| `icon()`      | A [Lucide](https://lucide.dev/icons) icon name.                   |
| `groups()`    | The `NavGroup`s that make up this tab's sidebar.                  |

## URLs

The tab key becomes a URL segment between the prefix and the slug:

```
/docs/{tab}/{slug}
/docs/guides/installation
/docs/api/users
```

When no tab is in the URL, Plume uses the **first** tab as the default and serves it at
the bare prefix.

::: note
Tabs replace `navigation()` as the source of the sidebar. If a vault defines `tabs()`,
the active tab's groups are shown. Versions can also carry their own tabs — see
[Versions](/docs/navigation/versions).
:::
