---
title: Introduction
description: "What Plume is, how the repository is split between package and playground, and the core idea."
---

# Introduction

Plume is a Laravel package that conveniently displays Markdown content in various scopes — a page on a route, a documentation folder, a wiki, a blog, and more. It is distributed on Packagist as `meetplume/plume` (`Meetplume\Plume\` namespace).

## The core idea

Content lives in Markdown files. Organization lives in PHP. Each **Vault** is a class that owns a URL prefix, a content path, a layout, and (optionally) navigation, tabs, versions and languages. Override a method and you unlock that layer — the `VaultRouter` adapts the URL shape for you. This is the same mental model as Filament resources: declarative classes, fluent builders, sensible defaults.

See [PHP Backend](/docs-contributing/architecture/php-backend) for the full anatomy of a Vault.

## How the repository is split

This repo is **two things at once**:

- **At root level** — the package itself (`src/`, `resources/`, `routes/`, `tests/`), the code shipped to Packagist.
- **In `playground/`** — a complete, plain Laravel 12 app (no starter kit, only Blade) that consumes the package the way a real project would, so we can develop and test against real UI.

The playground differs from a real consumer in two deliberate ways:

- It includes the package via a **symlink** (configured in `composer.repositories`), so any change to `src/` is visible immediately.
- It **does not gitignore `.env`** — the environment is committed so the setup is reproducible.

The Vite dev server runs from the **package root** but serves the playground app. See [Playground](/docs-contributing/getting-started/playground) for the wiring.

## Next steps

- [Development workflow](/docs-contributing/getting-started/development-workflow) — set up, run the dev server, run the tests, scaffold a vault.
- [Playground](/docs-contributing/getting-started/playground) — the vaults registered and how to iterate.

::: tip
Always update [`CHANGELOG.md`](https://github.com/meetplume/plume/blob/main/CHANGELOG.md) when you change behavior — the release and docs-sync workflows depend on it.
:::
</content>
