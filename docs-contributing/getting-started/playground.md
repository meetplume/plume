---
title: Playground
description: "How the playground Laravel app is wired up and how to test changes there."
---

# Playground

The `playground/` directory holds a full Laravel 12 app that consumes the package via **symlink** (configured in `composer.repositories`). It has no starter kit — only plain Blade — to keep things as close to a real consumer as possible.

## Differences from a real consuming project

- Includes the package via symlink (so any change to `src/` is visible immediately).
- **Does not gitignore `.env`** — it is committed so the environment is reproducible.
- Vite is **not** run from inside the playground — it is run from the package root and writes `playground/public/plume-hot` to enable HMR.

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
        LaravelZeroDocsVault::class,   // /laravel-zero/docs → content/laravel-zero
        MingleJsDocsVault::class,      // /minglejs          → content/minglejs
        DocsContributingVault::class,  // /docs-contributing → docs-contributing  (this site)
        DocsVault::class,              // /docs              → docs               (user guide)
    ]);
```

The vault classes live in `playground/app/Plume/`:

- **`LaravelZeroDocsVault`** mirrors the Laravel Zero docs in `content/laravel-zero` — a real, large, multi-group dataset. Custom `$home = '/laravel-zero'` and a `laravel-zero-homepage` page registered at the absolute `page`-layout route `/laravel-zero`.
- **`MingleJsDocsVault`** uses `content/minglejs` with a `homepage` mounted at the absolute route `/minglejs`.
- **`DocsContributingVault`** serves *this* developer documentation. Its `getPath()` is conditional: `vendor/meetplume/plume/docs-contributing` when running inside the playground, `content/docs-contributing` when running on the website (the file is synced to meetplume.com by CI).
- **`DocsVault`** serves the user-facing guide from the package's `docs/` folder, with the same conditional-path trick.

Every vault here overrides `canAccess(): true` — the default is `false`, so **keep that in mind when adding a new vault** or it will 403.

::: note
`DocsContributingVault` and `DocsVault` carry a header comment because CI copies them into the meetplume.com repository during the docs-sync workflow. Edit them here, in the package — not on the website.
:::

## Iterating on a change

1. Edit `src/` (PHP) or `resources/js/` (frontend) — the playground picks PHP up immediately via symlink; for the frontend, Vite reloads if `composer dev` is running.
2. Verify in the browser at <https://playground.test>.
3. Hit `GET /{prefix}/_plume` for any vault (local/test only) to get a JSON diagnostic dump — vault metadata, every slug, file existence, and the registered routes.
4. Write or update a test before finalizing.
</content>
