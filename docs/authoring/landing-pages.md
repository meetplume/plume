---
title: Landing Pages
description: Compose hero, features and call-to-action sections from frontmatter.
---

# Landing Pages

Any page can become a composed landing page by adding a `sections` array to its
[frontmatter](/docs/authoring/frontmatter). Sections render above the page's Markdown,
in order. This works with the `page` and `docs` layouts.

```md
---
sections:
    -   type: hero2
        title: Display Markdown The |Plume| Way
        tagline: "Pages, docs and wikis from plain Markdown — no database required."
        command: composer require meetplume/plume
        links:
            -   label: Get Started
                href: /docs/getting-started/installation
    -   type: features
        columns: { default: 1, md: 3 }
        features:
            -   title: Markdown First
                description: Author content in plain Markdown files.
                icon: FileText
---

Optional Markdown content renders below the sections.
```

## Section types

There are four section types: `hero1`, `hero2`, `features` and `callToAction`.

### `hero2`

A hero with an optional copy-to-clipboard command. Wrap part of the title in `|pipes|`
to highlight it.

```yaml
-   type: hero2
    title: Display Markdown The |Plume| Way
    tagline: Pages, docs and wikis from plain Markdown.
    command: composer require meetplume/plume
    links:
        -   label: Get Started
            href: /docs/getting-started/installation
```

| Key       | Type        | Description                                        |
|-----------|-------------|----------------------------------------------------|
| `title`   | string      | Headline. `|text|` marks a highlighted span.       |
| `tagline` | string      | Optional supporting line.                          |
| `command` | string      | Optional shell command with a copy button.         |
| `links`   | Link[]      | Optional call-to-action buttons.                   |

### `hero1`

A hero with an optional image instead of a command.

```yaml
-   type: hero1
    title: Build docs your way
    tagline: A flexible Markdown toolkit for Laravel.
    image: /images/hero.png
    imageDark: /images/hero-dark.png
    imageAlt: Screenshot of Plume
    links:
        -   label: Get Started
            href: /docs/getting-started/installation
```

| Key        | Type     | Description                              |
|------------|----------|------------------------------------------|
| `title`    | string   | Headline.                                |
| `tagline`  | string   | Optional supporting line.                |
| `links`    | Link[]   | Optional buttons.                        |
| `image`    | string   | Optional image (light mode).             |
| `imageDark`| string   | Optional image for dark mode.            |
| `imageAlt` | string   | Image alt text.                          |

### `features`

A responsive grid of features, each with an icon.

```yaml
-   type: features
    title: Everything you need
    description: A complete content toolkit.
    columns: { default: 1, md: 3 }
    features:
        -   title: Markdown First
            description: Author content in plain Markdown files.
            icon: FileText
        -   title: Vaults & Scopes
            description: Group content into vaults — a page, docs, a wiki.
            icon: Layers
        -   title: Laravel Native
            description: Routes, layouts and access control you already know.
            icon: Terminal
```

| Key           | Type        | Description                                                   |
|---------------|-------------|---------------------------------------------------------------|
| `title`       | string      | Optional section heading.                                     |
| `description` | string      | Optional section subheading.                                  |
| `columns`     | object      | Responsive column counts, e.g. `{ default: 1, md: 3 }`.       |
| `features`    | Feature[]   | Each has `title`, `description` and an optional `icon`.       |
| `footerTitle` | string      | Optional text below the grid.                                 |
| `footerLinks` | Link[]      | Optional links below the grid.                                |

### `callToAction`

A focused prompt with optional badge, image and links.

```yaml
-   type: callToAction
    badge: New
    title: Ship your docs today
    description: Install Plume and serve Markdown in minutes.
    links:
        -   label: Get Started
            href: /docs/getting-started/installation
            variant: default
```

| Key           | Type     | Description                          |
|---------------|----------|--------------------------------------|
| `badge`       | string   | Optional small label.                |
| `title`       | string   | Headline.                            |
| `description` | string   | Optional supporting text.            |
| `links`       | Link[]   | Optional buttons.                    |
| `image`       | string   | Optional image (light mode).         |
| `imageDark`   | string   | Optional image for dark mode.        |

## The Link shape

Every `links` entry shares the same shape:

| Key       | Type     | Description                                                      |
|-----------|----------|------------------------------------------------------------------|
| `label`   | string   | The link text. **Required.**                                     |
| `href`    | string   | The destination. **Required.**                                   |
| `target`  | string   | Optional, e.g. `_blank`.                                         |
| `variant` | string   | Button style: `default`, `secondary`, `outline`, `ghost`, etc.   |
| `icon`    | string   | Optional [Lucide](https://lucide.dev/icons) icon name.           |

::: tip
Icons for features and links use [Lucide](https://lucide.dev/icons) names. They're
matched flexibly, so `file-text` and `FileText` both work.
:::
