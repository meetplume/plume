---
title: Artisan Commands
description: The Artisan commands Plume adds to your application.
---

# Artisan Commands

## `plume:vault`

Scaffolds a new vault class and a starter content folder.

```bash
php artisan plume:vault Docs
```

| Argument | Description                                              |
|----------|----------------------------------------------------------|
| `name`   | The vault name, e.g. `Docs`, `Blog`, `BlogPosts`.        |

### What it generates

Given `php artisan plume:vault Docs`, the command creates:

- `app/Plume/DocsVault.php` — a vault served under `/docs` from `content/docs`, using
  the `docs` layout, with `canAccess()` returning `true`.
- `content/docs/index.md` — a starter index page (only if it doesn't already exist).

The name is normalized for each use:

| Input        | Class             | Prefix         | Content path           |
|--------------|-------------------|----------------|------------------------|
| `Docs`       | `DocsVault`       | `/docs`        | `content/docs`         |
| `BlogPosts`  | `BlogPostsVault`  | `/blog-posts`  | `content/blog-posts`   |

### After scaffolding

The command prints a reminder to register the vault in your
`AppServiceProvider::boot()`:

```php
use App\Plume\DocsVault;
use Meetplume\Plume\Facades\Plume;

Plume::configure()
    ->vaults([
        DocsVault::class,
    ]);
```

See the [Quick Start](/docs/getting-started/quick-start) for the full flow.

::: note
If the vault class already exists, the command stops without overwriting it.
:::

## `plume:update-assets`

Refreshes Plume's published front-end assets in your app's `public` directory.

```bash
php artisan plume:update-assets
```

It deletes `public/vendor/plume` and republishes the assets that ship with the
installed package version, ensuring stale hashed files are removed. Run it after
updating the package — see [Installation](/docs/getting-started/installation#updating-plume)
for how to wire it into a deploy script.
