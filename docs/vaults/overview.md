---
title: Vaults Overview
description: A vault maps a folder of Markdown files to a URL prefix. Learn its properties.
---

# Vaults Overview

A **vault** maps a folder of Markdown files to a URL prefix in your application.
You create one by extending `Meetplume\Plume\Vault`:

```php
<?php

namespace App\Plume;

use Illuminate\Http\Request;
use Meetplume\Plume\Vault;

class DocsVault extends Vault
{
    protected string $prefix = '/docs';

    protected string $path = 'content/docs';

    protected string $layout = 'docs';

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
```

Register it with the `Plume` facade (see [Quick Start](/docs/getting-started/quick-start)),
and every Markdown file under `content/docs` becomes a routable page under `/docs`.

## Properties

| Property     | Type            | Default     | Description                                              |
|--------------|-----------------|-------------|----------------------------------------------------------|
| `$prefix`    | `string`        | `'/'`       | The URL prefix the vault is served under.                |
| `$path`      | `string`        | `''`        | Folder holding the Markdown files (relative to project root, or absolute). |
| `$layout`    | `string`        | `'page'`    | The layout used to render pages. See [Layouts](/docs/appearance/layouts). |
| `$home`      | `?string`       | `null`      | Custom home URL. Defaults to the prefix.                 |
| `$discovery` | `Discovery`     | `Manual`    | How pages and navigation are resolved. See [Discovery](/docs/vaults/discovery). |

```php
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Vault;

class DocsVault extends Vault
{
    protected string $prefix = '/docs';

    protected string $path = 'content/docs';

    protected string $layout = 'docs';

    protected ?string $home = '/docs';

    protected Discovery $discovery = Discovery::Manual;
}
```

## Overridable methods

A vault declares its structure by overriding methods. All are optional except
`canAccess()`, which you almost always want to override.

| Method            | Returns               | Purpose                                                        |
|-------------------|-----------------------|----------------------------------------------------------------|
| `navigation()`    | `array<NavGroup>`     | The sidebar groups and pages. See [Sidebar](/docs/navigation/sidebar). |
| `pages()`         | `array<Page>`         | Extra routable pages not in the sidebar, and absolute routes. See [Pages](/docs/navigation/pages). |
| `tabs()`          | `array<Tab>`          | Top-level tabs, each with its own navigation. See [Tabs](/docs/navigation/tabs). |
| `versions()`      | `array<Version>`      | Versioned documentation. See [Versions](/docs/navigation/versions). |
| `languages()`     | `array<Language>`     | Localized documentation. See [Languages](/docs/navigation/languages). |
| `canAccess()`     | `bool`                | Authorization gate. See [Access Control](/docs/vaults/access-control). |
| `pageActions()`   | `array<PageAction>`   | The entries of the page menu. See [Page Actions](/docs/vaults/page-actions). |

## Multiple vaults

Vaults are fully independent. Add as many as you need — one for marketing pages,
another for product docs, another for a wiki — each with its own prefix, content
folder, layout and navigation:

```php
Plume::configure()
    ->vaults([
        MarketingVault::class,
        DocsVault::class,
        WikiVault::class,
    ]);
```

## Custom content path

`getPath()` can be overridden to compute the path dynamically — useful when the same
vault serves from different locations in different environments:

```php
public function getPath(): string
{
    if (basename(base_path()) === 'playground') {
        return 'vendor/meetplume/plume/docs';
    }

    return 'content/docs';
}
```

Paths are resolved relative to `base_path()`. An absolute path (starting with `/`)
is used as-is.
