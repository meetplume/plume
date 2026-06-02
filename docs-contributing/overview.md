---
title: Overview
description: "Map of the Plume codebase — package source, playground, frontend assets, and how it all fits."
---

# Overview

Plume is a Laravel package (Packagist `meetplume/plume`, namespace `Meetplume\Plume\`) for serving Markdown content as pages, docs, wikis and more. It follows a **PHP-first, Filament-inspired** architecture where Vault classes are the unit of organization.

## Repository map

```
plume/
├── src/                  → Package code (PHP)
│   ├── Plume.php                   ← Entry point / singleton holding the configuration
│   ├── PlumeServiceProvider.php    ← Boot, views, Blade directives, asset publishing, commands
│   ├── PlumeConfiguration.php      ← Fluent config (name, theme, header, footer, vaults)
│   ├── Vault.php / VaultRouter.php ← Organization unit + route registration
│   ├── Page.php, NavGroup.php, Tab.php, Version.php, Language.php
│   ├── Header.php, Footer.php, FooterColumn.php, Social.php
│   ├── FilesystemScanner.php       ← Recursive .md scanner (Discovery::Mapped/Auto)
│   ├── Frontmatter.php             ← YAML frontmatter parser
│   ├── ThemeConfig.php             ← Preset resolution + customizer
│   ├── Enums/Discovery.php         ← Manual | Mapped | Auto
│   ├── Enums/CodeTheme.php         ← Expressive Code theme names
│   ├── Actions/                    ← SearchIndexBuilder, SearchIndexCache
│   ├── Commands/MakeVaultCommand.php  ← `php artisan plume:vault {name}`
│   ├── Http/Controllers/           ← VaultPage, Diagnostics, ContentAsset, SearchIndex, Customizer
│   ├── Inertia/                    ← PlumeInertia (own Inertia, no inertia-laravel)
│   └── Facades/Plume.php
│
├── resources/
│   ├── js/                ← React + Inertia (TSX)
│   │   ├── app.tsx
│   │   ├── pages/plume/   ← Layouts: docs, page, blog, api, wiki, changelog
│   │   ├── components/blocks/  ← hero1/2, features, header1, footer1/2, call-to-action, section
│   │   ├── components/plume/   ← markdown-renderer, sidebar-nav, docs-header/footer, toc, search-*
│   │   ├── components/customizer/  ← Local-only panel to tweak theme
│   │   ├── hooks/         ← use-plume-search, use-search-shortcut
│   │   └── components/ui/, editor/
│   ├── views/app.blade.php    ← Root view with @plumeInertia / @plumeAssets
│   ├── presets/*.yml          ← brutalist, catppuccin, default, forest, ocean, rose
│   └── stubs/*.stub           ← Templates used by `plume:vault`
│
├── routes/customizer.php  → POST /_plume/customizer (local/test only)
├── docs/                  → User-facing guide (served by DocsVault)
├── docs-contributing/     → This documentation (served by DocsContributingVault)
├── playground/            → Full Laravel app consuming the package via symlink
├── tests/                 → Pest (Unit + Feature) with Orchestra Testbench
├── solo.yml               → Solo-managed `npm:dev` process
└── vite.config.ts         → Build to dist/ → published to public/vendor/plume/dist
```

## Reading order

If this is your first time in the codebase, follow this order:

1. **[Introduction](/docs-contributing/getting-started/introduction)** — what the project is and how it's split.
2. **[Development workflow](/docs-contributing/getting-started/development-workflow)** — `composer setup`, `composer dev`, tests, `plume:vault`.
3. **[Playground](/docs-contributing/getting-started/playground)** — how the test app is wired up.
4. **[PHP Backend](/docs-contributing/architecture/php-backend)** — ServiceProvider, Plume, Configuration, Vault, Router, Controllers, Inertia, Theme, Search.
5. **[Frontend Pipeline](/docs-contributing/architecture/frontend)** — Vite, Inertia, React layouts, blocks, markdown renderer, search UI.
6. **[Search](/docs-contributing/architecture/search)** — how the per-vault search index is built, cached and served.
7. **[Roadmap](/docs-contributing/roadmap)** — what's done vs. what the ADR still proposes.
8. **[ADR-001](/docs-contributing/adr/001-markdown-pages-blocks-system)** — the source of truth for the architectural direction.

## At-a-glance facts

- **PHP-first à la Filament** — each `Vault` is a class; overriding methods (`languages → versions → tabs → navigation → pages`) unlocks layers, and the `VaultRouter` adapts the URL automatically (FULL → FLAT).
- **Discovery in 3 modes** — `Manual` (default, all PHP), `Mapped` (routes from disk, sidebar from PHP), `Auto` (everything derived from the filesystem).
- **Own Inertia** — since 0.6.0, no dependency on `inertia-laravel`.
- **Isolated assets** — since 0.9.0, Plume ships its own Vite instance via `@plumeAssets` and a dedicated `public/plume-hot` file, so it never collides with the consuming app's own Vite dev server.
- **Routes register in console too** — since 0.9.2 vault routes show in `route:list` and survive `route:cache` (they use controllers, not closures).
- **Vaults are private by default** — `canAccess()` returns `false`; every vault must override.
- **Scaffolding** — `php artisan plume:vault {name}` generates a starter vault class and `content/{name}/index.md`.
- **Diagnostics & search** — `GET {prefix}/_plume` (local/test only) returns JSON metadata; `GET {prefix}/_plume/search-index.json` serves the cached search index.
</content>
