# Installation

Install Plume into your Laravel application via Composer:

```bash
composer require meetplume/plume
```


## Scaffold your first vault

With Plume installed, the fastest way to get started is the `plume:vault` command:

```bash
php artisan plume:vault Docs
```

This scaffolds two things for you:

- `app/Plume/DocsVault.php` — a vault class served under the `/docs` prefix, with
  filesystem auto-discovery enabled.
- `content/docs/index.md` — a starter index page, so the vault renders something right away.

The command accepts multi-word names too — `php artisan plume:vault BlogPosts` creates a
`BlogPostsVault` served under `/blog-posts` from `content/blog-posts`.

The last step is to register the vaultso Plume knows to serve it.

## Vault registration

Register your vaults with the `Plume` facade so they are served by your application:

You just have to add the following to you `AppServiceProvider` file:

```php
use App\Plume\DocsGuideVault;
use Meetplume\Plume\Facades\Plume;

Plume::configure()
    ->vaults([
        DocsGuideVault::class,
    ]);
```

In the end, it may look something like this:

```php
<?php

namespace App\Providers;

use App\Plume\DocsGuideVault;
use Illuminate\Support\ServiceProvider;
use Meetplume\Plume\Facades\Plume;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Plume::configure()
            ->vaults([
                DocsGuideVault::class,
            ]);
    }
}

```

Each vault is independent: it has its own URL prefix, content folder, layout and navigation. Add as
many vaults as you need — one for a marketing page, another for product docs, another for a wiki.
