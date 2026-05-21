---
title: PHP Backend
description: "Service provider, configuration, vaults, router, controllers, Inertia and theme — everything PHP."
---

# PHP Backend

## Service provider — `src/PlumeServiceProvider.php`

- `register()` binds `Plume` and `PlumeInertia` as singletons.
- `boot()`:
  - `loadViewsFrom('resources/views', 'plume')` — exposes the `plume::*` namespace.
  - `publishes(__DIR__.'/../dist' → public_path('vendor/plume/dist'))` with tag `plume-assets`.
  - Registers Blade directives `@plumeInertia` and `@plumeInertiaHead`.
  - **If `runningInConsole()` → returns early.** No routes or config boot under CLI (added in 0.7.0).
  - In `local|testing` loads `routes/customizer.php`.
  - On `app->booted()`, boots `PlumeConfiguration` (this is where vault routes get registered).

## Entry point — `src/Plume.php`

A small singleton with three methods: `configure()`, `getConfiguration()`, `getVault(prefix)`. The facade `Meetplume\Plume\Facades\Plume` points here.

## Fluent configuration — `src/PlumeConfiguration.php`

Filament-style chained setup:

```php
Plume::configure()
    ->name('My Project')
    ->url(...)->logo(...)->favicon(...)
    ->theme('ocean')
    ->header(
        Header::make('header1')
            ->cta(...)
            ->socials([...])
    )
    ->footer(
        Footer::make('footer1')->columns([...])
    )
    ->vaults([DocsVault::class, BlogVault::class]);
```

`boot()` does two things:

1. Initialises the theme: reads `resources/presets/{theme}.yml` and binds `ThemeConfig` in the container.
2. Creates a `VaultRouter` and — **inside `Route::middleware('web')->group(...)`** — instantiates each vault class and registers its routes. (The `web` middleware wrap was added recently — see CHANGELOG Unreleased.)

## Vault — `src/Vault.php`

The unit of organization for content. Each vault is a class extending `Vault` that defines properties and optional methods.

### Properties

- `$prefix` — URL root segment (e.g. `/docs`).
- `$path` — content path relative to `base_path()`.
- `$layout` — `docs | page | blog | api | wiki | changelog`.
- `$home` — optional home URL.
- `$discovery` — `Discovery::Manual` (default), `Mapped` or `Auto`.

### Overridable methods (each one unlocks a layer)

- `navigation(): NavGroup[]` — manual sidebar.
- `pages(): Page[]` — extra pages (and pages registered at an absolute `route()`).
- `tabs(): Tab[]` — tabs with their own groups.
- `versions(): Version[]` — `v1`, `v2`, etc., optionally with their own tabs.
- `languages(): Language[]` — i18n with optional translated slugs.
- `canAccess(Request): bool` — **default `false`**. Every vault must override to be reachable.

### Resolution

`resolveNavigation()` and `resolvePages()`:

1. If `Discovery::Auto` → derived from the filesystem via `FilesystemScanner`.
2. Otherwise, if `versions()` / `tabs()` are defined → pick the active tabs (a version can have its own).
3. Otherwise → fall back to `navigation()`.

### Helpers

- `collectAllSlugs()` — used by the router to build regex constraints.
- `resolveFilePath(Page, ?lang, ?ver)` — joins `lang/`, `version/` into the final path.
- `getDefaultLanguage()`, `getDefaultVersion()` — first `->default()` or first in the list.

## Router — `src/VaultRouter.php`

Registers **one parametric route per URL variant per vault**, not one per page. Constraints come from `collectAllSlugs()`. Variants:

```
FULL:    /{prefix}/{lang}/{version}/{tab}/{slug}
NO TAB:  /{prefix}/{lang}/{version}/{slug}
NO VER:  /{prefix}/{lang}/{slug}
NO LANG: /{prefix}/{slug}
FLAT:    /{prefix}/{slug}
```

It also registers:

- **Root route** when there is a slug `/` (`plume.{prefix}.root`).
- **`no-lang`, `no-ver`, `no-lang-ver`** variants so default values can stay out of the URL.
- **Absolute routes** for pages declared with `Page::route('/foo')`.
- **Diagnostics** — `GET {prefix}/_plume`, local/test only. Returns a JSON dump of the vault.
- **Content assets** — `GET {prefix}/_content/{path}` for images and other static files alongside the markdown.

All routes carry `defaults('vaultPrefix', $prefix)` so the controller knows which vault to use.

## VaultPageController — `src/Http/Controllers/VaultPageController.php`

`__invoke(Request)`:

1. Read `vaultPrefix` from the route defaults → fetch the vault → `abort_unless($vault?->canAccess($request))`.
2. Extract `language|version|tab|slug` from the route and fill in defaults.
3. `resolvePages(language, version, tab)` → look up the `Page` by slug.
4. `resolveFilePath()` → read the `.md` file and parse its frontmatter via `Page::toInertiaProps`.
5. Build Inertia props: `navigation`, `tabs`, `versions`, `languages`, `prev/next`, `header`, `footer`, `site`, `contentAssetBase`, `sections` (from frontmatter), `plume` (theme + customizer).
6. Render via `PlumeInertia::render('plume/'.$layout, $props)`.

## Discovery enum — `src/Enums/Discovery.php`

- `Manual` — pure PHP (navigation/tabs/versions/pages).
- `Mapped` — routes come from the filesystem; sidebar stays manual.
- `Auto` — everything derived from the filesystem. `FilesystemScanner::scanNavigation()` groups by directory and orders by frontmatter `order`, falling back to alphabetical.

## Own Inertia — `src/Inertia/PlumeInertia.php`

Minimal in-house Inertia implementation. Since 0.6.0 the package **no longer depends on `inertiajs/inertia-laravel`**. `PlumeInertia` holds shared props and the root view; `PlumeInertiaResponse` renders the Blade root with the `data-page` JSON payload.

## Theme — `src/ThemeConfig.php`

Reads YAML from `resources/presets/{name}.yml` with override from the vault's own `config.yml`. Supports `primary, gray, radius, spacing, dark, code_theme_light/dark, customizer`. Exposed to the frontend via shared prop `plume.theme`. The customizer panel only ships if `app()->isLocal()`.

## Domain helpers

`Page`, `NavGroup`, `Tab`, `Version`, `Language`, `Header`, `Footer` all expose a `make($key)` static constructor, fluent setters returning `self`/`static`, and `getX()` readers.
