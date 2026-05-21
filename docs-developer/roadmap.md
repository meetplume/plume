---
title: Roadmap
description: "What's already implemented in Plume vs. what ADR-001 still proposes."
---

# Roadmap

Reference ADR: [`adr/001-markdown-pages-blocks-system`](/dev-docs/adr/001-markdown-pages-blocks-system) — status **In discussion**.

## Already implemented

- Vaults with the full hierarchy `languages → versions → tabs → navigation → pages` (every layer optional).
- Router building the 5 URL variants (FULL → FLAT) plus root, absolute, diagnostics and content-asset routes.
- Discovery modes `Manual | Mapped | Auto` with `FilesystemScanner` (recursive, frontmatter-aware, ordered).
- Per-vault `config.yml` for theme and customizer (local only).
- React layouts: `docs`, `page`, `blog`, `api`, `wiki`, `changelog`.
- Built-in blocks via the `sections` frontmatter array (`hero1/2`, `features`, `cta`, `header1`, `footer1/2`).
- Own Inertia implementation (`PlumeInertia`) — no external dependency.
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
- Auto sitemap, RSS/Atom for blogs, robots.txt, search provider, custom 404.
- Draft status via frontmatter.
- Configurable language file strategy (subfolder vs `.pt.md` suffix).

## Release history snapshot

- **0.7.0** (2026-03-17) — `runningInConsole` early-return in the ServiceProvider.
- **0.6.0** (2026-03-17) — Own Inertia, `Page::home/route/layout`, `Vault::getHome`, Testbench-based tests.
- **0.5.0** (2026-03-16) — Vault system replacing Collections, parametric VaultRouter, tabs/versions/languages, Discovery enum + FilesystemScanner, multiple layouts, diagnostics.

See `CHANGELOG.md` at the repo root for the full history.
