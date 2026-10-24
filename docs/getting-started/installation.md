---
title: Installation
description: Install Plume into your Laravel application via Composer.
---

# Installation

Plume is a Laravel package. Install it into any Laravel application with Composer:

```bash
composer require meetplume/plume
```

That's it — there is nothing to publish and no database to migrate. Plume serves
Markdown files straight from your project.

## Requirements

- PHP 8.3+
- Laravel 11+

## Updating Plume

Plume ships its compiled front-end assets (the docs UI, search palette, etc.) with
the package. Whenever you update the package, refresh the copy published into your
app's `public` directory so browsers receive the new build:

```bash
composer update meetplume/plume
php artisan plume:update-assets
```

`plume:update-assets` removes the previously published assets and republishes the
current ones, so stale hashed files don't linger. Add it to your deploy script,
**after** `composer install`, so production always serves the assets that match the
installed version:

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan plume:update-assets
```

::: note
Skipping this step is a common cause of "production looks outdated" issues — for
example the search palette serving a smaller, older index than your local copy.
:::

## Laravel Boost

Plume ships a guideline and a `plume-development` skill for
[Laravel Boost](https://github.com/laravel/boost), so that coding agents know how to
create a vault and write its pages. Select `meetplume/plume` when Boost asks which
packages to include, in `php artisan boost:install` or `php artisan boost:update`.

## Next steps

Head to the [Quick Start](/docs/getting-started/quick-start) to scaffold your first
vault and see content on screen in under a minute.
