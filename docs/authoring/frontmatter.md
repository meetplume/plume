---
title: Frontmatter
description: YAML frontmatter keys that control titles, slugs, ordering and more.
---

# Frontmatter

Every Markdown file may begin with a block of YAML **frontmatter**, fenced by `---`.
Plume reads it to set page metadata, control navigation, and drive special layouts.

```md
---
title: Installing Plume
description: How to add Plume to your Laravel application.
---

# Installing Plume

Your content starts here…
```

## Common keys

| Key           | Type      | Used for                                                            |
|---------------|-----------|---------------------------------------------------------------------|
| `title`       | string    | Page `<title>` and nav label. Without it, the first `# ` heading.   |
| `description` | string    | The `<meta name="description">` tag.                                |
| `slug`        | string    | The URL slug (in [Auto/Mapped discovery](/docs/vaults/discovery)).  |
| `order`       | int       | Sort order in the sidebar (in Auto discovery).                      |
| `hidden`      | bool      | Keep the page routable but hide it from the sidebar.                |
| `group_icon`  | string    | The [Lucide](https://lucide.dev/icons) icon for the page's group (Auto discovery). |
| `group_label` | string    | The label of the page's group, instead of the humanized folder name (Auto discovery). |

::: tip
In **Manual discovery**, the `Page` builder (`->label()`, `->slug()`, `->order()`,
`->hidden()`) takes precedence — frontmatter `title` is still used as a label fallback.
In **Auto discovery**, frontmatter is the primary way to control each page. See
[Discovery Modes](/docs/vaults/discovery).
:::

## Layout-specific keys

Some [layouts](/docs/appearance/layouts) read extra keys:

```md
---
title: Plume 1.0 is here
date: 2026-05-31
author: Jane Doe
---
```

- `date` — shown by the `blog` and `changelog` layouts.
- `author` — shown by the `blog` layout.

## Landing page sections

The `sections` key turns a page into a composed landing page (hero, features, etc.)
instead of plain prose. This has its own page: see
[Landing Pages](/docs/authoring/landing-pages).

```md
---
sections:
    -   type: hero2
        title: Display Markdown The |Plume| Way
        tagline: Pages, docs and wikis from plain Markdown.
---
```
