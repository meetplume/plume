---
title: Discovery Modes
description: Control how Plume resolves pages and navigation — Manual, Mapped, or Auto.
---

# Discovery Modes

A vault's `$discovery` property controls how Plume turns your content into routes and
navigation. There are three modes, defined by the `Meetplume\Plume\Enums\Discovery` enum.

```php
use Meetplume\Plume\Enums\Discovery;

class DocsVault extends Vault
{
    protected Discovery $discovery = Discovery::Auto;
}
```

## `Discovery::Manual` (default)

Everything is declared in PHP. Pages come from `navigation()`, `tabs()`, `versions()`
and `pages()`. The filesystem is never scanned — a Markdown file with no matching
`Page` declaration is not routable.

Use it when you want full, explicit control over which pages exist and their order.

```php
protected Discovery $discovery = Discovery::Manual;

public function navigation(): array
{
    return [
        NavGroup::make('getting-started')->pages([
            Page::make('index')->home(),
            Page::make('installation'),
        ]),
    ];
}
```

## `Discovery::Mapped`

Routes are **discovered from the `.md` files on disk** (recursively), so every file is
reachable without declaring it. Your `navigation()` is still used to build the sidebar
UI, and `pages()` provides overrides.

Use it when you want every file to be routable, but still want to hand-curate the
sidebar.

```php
protected Discovery $discovery = Discovery::Mapped;
```

## `Discovery::Auto`

Both routes **and** navigation are derived from the filesystem. Directory names become
nav groups, and `.md` files become pages. You write zero PHP for navigation — the
folder structure *is* the navigation.

```php
protected Discovery $discovery = Discovery::Auto;
```

With this content folder:

```
content/docs/
├── index.md
├── getting-started/
│   ├── installation.md
│   └── configuration.md
└── guides/
    └── deployment.md
```

Plume produces a "Getting Started" group and a "Guides" group automatically.

### Frontmatter controls

In Auto mode, [frontmatter](/docs/authoring/frontmatter) on each file fine-tunes the
result:

```yaml
---
title: Installation Guide   # overrides the humanized filename
slug: install               # overrides the URL slug
order: 1                    # sorts pages (and their group) — lower comes first
hidden: true                # keep routable, but hide from the sidebar
group_icon: rocket          # sets the icon for the page's group
---
```

::: note
- Files and folders prefixed with `_` (e.g. `_draft.md`) are skipped during scanning.
- A root-level `index.md` is automatically mapped to the vault's home (`/`).
- Pages without an explicit `order` sort alphabetically, after ordered pages.
:::
