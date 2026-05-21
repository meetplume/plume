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
│   ├── Plume.php                   ← Entry point / singleton with configuration
│   ├── PlumeServiceProvider.php    ← Boot, views, customizer routes, asset publishing
│   ├── PlumeConfiguration.php      ← Fluent config (name, theme, header, footer, vaults)
│   ├── Vault.php / VaultRouter.php ← Organization unit + route registration
│   ├── Page.php, NavGroup.php, Tab.php, Version.php, Language.php
│   ├── Header.php, Footer.php, FooterColumn.php, Social.php
│   ├── FilesystemScanner.php       ← Recursive .md scanner (Discovery::Auto/Mapped)
│   ├── Frontmatter.php             ← YAML frontmatter parser
│   ├── ThemeConfig.php             ← Preset resolution + customizer
│   ├── Enums/Discovery.php         ← Manual | Mapped | Auto
│   ├── Http/Controllers/           ← VaultPage, Diagnostics, ContentAsset, Customizer
│   ├── Inertia/                    ← PlumeInertia (replaces inertia-laravel)
│   └── Facades/Plume.php
│
├── resources/
│   ├── js/                ← React + Inertia (TSX)
│   │   ├── app.tsx
│   │   ├── pages/plume/   ← Layouts: docs, page, blog, api, wiki, changelog
│   │   ├── components/blocks/  ← hero1, hero2, features, header1, footer1/2, cta, section
│   │   ├── components/plume/   ← markdown-renderer, sidebar-nav, docs-header/footer, toc
│   │   ├── components/customizer/  ← Local-only panel to tweak theme
│   │   └── components/ui/, editor/
│   ├── views/app.blade.php    ← Root view with @plumeInertia
│   └── presets/*.yml          ← brutalist, catppuccin, default, forest, ocean, rose
│
├── routes/customizer.php  → POST /_plume/customizer (local/test only)
├── docs-developer/        → This documentation (what you are reading)
├── playground/            → Full Laravel app consuming the package via symlink
├── tests/                 → Pest (Unit + Feature) with Orchestra Testbench
├── solo.yml               → Solo-managed `npm:dev` process
└── vite.config.ts         → Build to dist/ → published to public/vendor/plume/dist
```

## Reading order

If this is your first time in the codebase, follow this order:

1. **[Introduction](/dev-docs/getting-started/introduction)** — what the project is and the dev workflow.
2. **[Development workflow](/dev-docs/getting-started/development-workflow)** — `composer setup`, `composer dev`, tests.
3. **[Playground](/dev-docs/getting-started/playground)** — how the test app is wired up.
4. **[PHP Backend](/dev-docs/architecture/php-backend)** — ServiceProvider, Plume, Configuration, Vault, Router, Controller, Inertia, Theme.
5. **[Frontend Pipeline](/dev-docs/architecture/frontend)** — Vite, Inertia, React layouts, blocks, markdown renderer.
6. **[Roadmap](/dev-docs/roadmap)** — what's done vs. what the ADR proposes.
7. **[ADR-001](/dev-docs/adr/001-markdown-pages-blocks-system)** — the source of truth for the architectural direction.

## At-a-glance facts

- **PHP-first à la Filament** — each `Vault` is a class; overriding methods (`languages → versions → tabs → navigation → pages`) unlocks layers, and the `VaultRouter` adapts the URL automatically (FULL → FLAT).
- **Discovery in 3 modes** — `Manual` (all PHP), `Mapped` (routes from disk, sidebar from PHP), `Auto` (everything derived from filesystem).
- **Own Inertia** — since 0.6.0, no dependency on `inertia-laravel`.
- **Vaults are private by default** — `canAccess()` returns `false`; every vault must override.
- **Diagnostics endpoint** — `GET {prefix}/_plume` returns JSON with slugs, paths, routes (local/test only).
