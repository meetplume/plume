---
title: Header
description: Configure the site header — links, a call-to-action, and social icons.
---

# Header

The header is configured globally on the `Plume` facade and shown across all vaults.
Build one with `Header::make()` and pass it to `->header()`:

```php
use Meetplume\Plume\Facades\Plume;
use Meetplume\Plume\Header;
use Meetplume\Plume\Social;

Plume::configure()
    ->header(
        Header::make('header1')
            ->links([
                ['label' => 'Docs', 'href' => '/docs'],
                ['label' => 'Blog', 'href' => '/blog'],
            ])
            ->cta('Get Started', '/docs/getting-started/installation')
            ->socials([
                Social::github('https://github.com/meetplume/plume'),
            ])
    )
    ->vaults([ /* ... */ ]);
```

## Header builder

| Method            | Description                                                       |
|-------------------|-------------------------------------------------------------------|
| `make($component)`| The header component to render, e.g. `header1`.                   |
| `links([...])`    | Navigation links — an array of `['label' => …, 'href' => …]`.     |
| `cta($label, $href)` | A prominent call-to-action button.                             |
| `socials([...])`  | Social icons — an array of `Social` objects.                      |

## Social icons

`Social` has convenience constructors for common networks, plus a generic `make()`:

```php
use Meetplume\Plume\Social;

Social::github('https://github.com/meetplume/plume');
Social::x('https://x.com/meetplume');
Social::discord('https://discord.gg/…');
Social::youtube('https://youtube.com/@…');
Social::bluesky('https://bsky.app/profile/…');

// Any other icon:
Social::make('rss', 'https://example.com/feed.xml');
```

Each `Social` takes an icon name and a URL. The convenience methods simply preset the
icon.

::: note
The header's logo and site name come from the global branding configuration — see
[Branding](/docs/site/branding).
:::
