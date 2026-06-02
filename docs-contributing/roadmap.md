---
title: Roadmap
description: "What's already implemented in Plume vs. what ADR-001 still proposes."
---

# Roadmap

Reference ADR: [`adr/001-markdown-pages-blocks-system`](/docs-contributing/adr/001-markdown-pages-blocks-system) — status **In discussion**.

## Already implemented

- Vaults with the full hierarchy `languages → versions → tabs → navigation → pages` (every layer optional).
- Router building the URL variants (FULL → FLAT) plus root, absolute, diagnostics, search-index and content-asset routes — registered in console too, so they show in `route:list` and survive `route:cache`.
- Discovery modes `Manual | Mapped | Auto` with `FilesystemScanner` (recursive, frontmatter-aware, ordered).
- Per-vault `config.yml` for theme and customizer (local only).
- React layouts: `docs`, `page`, `blog`, `api`, `wiki`, `changelog`.
- Built-in blocks via the `sections` frontmatter array (`hero1/2`, `features`, `call-to-action`, `header1`, `footer1/2`).
- Own Inertia implementation (`PlumeInertia`) — no external dependency.
- Isolated Vite assets via `@plumeAssets` + dedicated `plume-hot` file (no collision with the consuming app's Vite).
- Client-side search (MiniSearch) with a cached per-vault index served from `{prefix}/_plume/search-index.json`.
- `php artisan plume:vault {name}` scaffolding command.
- Prev/Next and breadcrumbs derived from navigation.

## Described in the ADR but not yet implemented

### `@` syntax (ADR §4)

- Block fences like `` ```@hero ``, `` ```@features ``, `` ```@include ``, `` ```@code `` with YAML props + Markdown slot separated by `---`.
- Named slots (`#default`, `#aside`).
- Inline refs `[text](@glossary/plume)` for tooltips/popups.
- `@ref id` block for complex inline-ref props.

### Server-side Markdown preprocessing (ADR §5)

- Recursive `@include` resolution with cycle protection (`maxDepth`).
- `@code` embedding of real source code (`lines`, `highlight`, `from/to` markers, `title`).
- Variable interpolation `{{ site.name }}`, `{{ version }}` with cascade global→vault→folder→page.
- Inline-ref resolution in PHP before payload reaches React.

### Custom blocks (ADR §6)

- Convention-based discovery from `resources/js/components/blocks/*`.
- Dynamic frontend block registry (`{...builtIn, ...custom}`).
- PHP `BlockResolver` interface for dynamic/computed data.
- External `data: data/pricing.yml` loader (today only inline frontmatter works).

### Other (ADR §8)

- Page data files (`page-name.yml` sibling).
- Computed props (reading time, last modified via git).
- Conditional content (`if: hasApiKey`).
- Shared block wrappers (background, container width, anchor ID).
- Data cascade global→vault→folder/index.yml→page.
- Auto sitemap, RSS/Atom for blogs, robots.txt, custom 404.
- Draft status via frontmatter.
- Configurable language file strategy (subfolder vs `.pt.md` suffix).

## Release history snapshot

- **0.10.0** (2026-05-29) — `plume:vault` scaffolding command; docs-sync workflow publishes docs (and the example vault) to meetplume.com; `getAbsolutePath()` resolves through `getPath()`.
- **0.9.0** (2026-05-29) — isolated assets (`@plumeAssets`, `plume-hot`); docs split into `docs` (user guide) and `docs-contributing` (this site); `sync-docs` CI workflow; routes registered in console (0.9.2).
- **0.8.0** (2026-05-28) — client-side search (MiniSearch) with `SearchIndexController`.
- **0.7.1** (2026-03-26) — `web` middleware on Plume routes.
- **0.6.0** (2026-03-17) — own Inertia, `Page::home/route/layout`, `Vault::getHome`, Testbench-based tests.
- **0.5.0** (2026-03-16) — Vault system replacing Collections, parametric VaultRouter, tabs/versions/languages, Discovery enum + FilesystemScanner, multiple layouts, diagnostics.

See `CHANGELOG.md` at the repo root for the full history.
</content>
