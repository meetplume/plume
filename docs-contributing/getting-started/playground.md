---
title: Playground
description: "How the playground Laravel app is wired up and how to test changes there."
---

# Playground

The `playground/` directory holds a full Laravel 12 app that consumes the package via **symlink** (configured in `composer.repositories`). It has no starter kit — only plain Blade — to keep things as close to a real consumer as possible.

## Differences from a real consuming project

- Includes the package via symlink (so any change to `src/` is visible immediately).
- **Does not gitignore `.env`** — it is committed so the environment is reproducible.
- Vite is **not** run from inside the playground — it is run from the package root and writes `playground/public/hot` to enable HMR.

## Dev URL

<https://playground.test> via Herd. Use the agent-browser skill to drive it programmatically when verifying UI changes.

## Vaults registered

`playground/app/Providers/AppServiceProvider.php`:

```php
Plume::configure()
    ->name('Plume Playground')
    ->theme('default')
    ->header(Header::make('header1')->socials([Social::github(...)]))
    ->footer(Footer::make('footer1')->text('Built with Plume'))
    ->vaults([
        PlumeDocsVault::class,         // /plume/docs       → vendor/meetplume/plume/docs
        LaravelZeroDocsVault::class,   // /laravel-zero/... → content/laravel-zero
        MingleJsDocsVault::class,      // /minglejs         → content/minglejs
        DocsDeveloperVault::class,     // /dev-docs         → docs-developer  (this site)
    ]);
```

- **`PlumeDocsVault`** points to the package's own `docs/` folder via the symlinked vendor path. Manual navigation with `getting-started` + `how-to-write`.
- **`LaravelZeroDocsVault`** mirrors the Laravel Zero docs in `content/laravel-zero` — a real, large, multi-group dataset. Custom `$home = '/laravel-zero'` and a `laravel-zero-homepage` registered at the absolute route `/laravel-zero`.
- **`MingleJsDocsVault`** uses `content/minglejs` with a `homepage` mounted at absolute route `/minglejs`.
- **`DocsDeveloperVault`** is what serves this developer documentation.

All three production vaults override `canAccess(): true` (the default is `false` — keep that in mind when adding a new vault).

## Iterating on a change

1. Edit `src/` (PHP) or `resources/js/` (frontend) — playground picks PHP up immediately via symlink; for the frontend, Vite reloads if `composer dev` is running.
2. Verify in the browser at <https://playground.test>.
3. Hit `/{prefix}/_plume` for any vault to get a JSON diagnostic dump (vault metadata, all slugs, file existence, registered routes).
4. Write or update a test before finalizing.
