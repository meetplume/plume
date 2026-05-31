---
title: Quick Start
description: Scaffold a vault, register it, and render your first Markdown page.
---

# Quick Start

A **vault** is the core idea in Plume: it maps a folder of Markdown files to a URL
prefix in your app. Let's create one.

## 1. Scaffold a vault

The fastest way to start is the `plume:vault` command:

```bash
php artisan plume:vault Docs
```

This generates two things:

- `app/Plume/DocsVault.php` — a vault class served under the `/docs` prefix.
- `content/docs/index.md` — a starter page, so the vault renders immediately.

The name is flexible. `php artisan plume:vault BlogPosts` creates a `BlogPostsVault`
served under `/blog-posts` from `content/blog-posts`.

## 2. Register the vault

Register your vaults with the `Plume` facade in your `AppServiceProvider::boot()`
method, so your application knows to serve them:

```php
<?php

namespace App\Providers;

use App\Plume\DocsVault;
use Illuminate\Support\ServiceProvider;
use Meetplume\Plume\Facades\Plume;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Plume::configure()
            ->vaults([
                DocsVault::class,
            ]);
    }
}
```

## 3. Visit your content

Start your app and visit `/docs`. The starter `index.md` renders right away.

```bash
php artisan serve
```

## What's next

- Add more vaults — one per scope (marketing page, product docs, a wiki). Each is
  independent: its own prefix, folder, layout and navigation.
- Learn how a vault is configured in [Vaults → Overview](/docs/vaults/overview).
- Build a sidebar in [Navigation → Sidebar](/docs/navigation/sidebar).

::: tip
Every vault you create must be added to the `->vaults([...])` array. Forgetting to
register it is the most common reason a vault returns a 404.
:::
