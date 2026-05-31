---
title: Layouts
description: The built-in layouts — page, docs, blog, wiki, api and changelog.
---

# Layouts

A layout decides how a page's content is framed: whether it has a sidebar, a table of
contents, a title header, and so on. Set a default layout on the vault, or override it
per page.

```php
// Vault default
class DocsVault extends Vault
{
    protected string $layout = 'docs';
}
```

```php
// Per-page override
Page::make('changelog')->layout('changelog');
```

## Available layouts

| Layout      | Sidebar | TOC | Best for                                                        |
|-------------|:-------:|:---:|-----------------------------------------------------------------|
| `page`      |   —     |  —  | Landing/marketing pages. Renders [sections](/docs/authoring/landing-pages) then prose. |
| `docs`      |   ✓     |  ✓  | Documentation. Sidebar, table of contents, prev/next, plus tabs/versions/languages. |
| `wiki`      |   ✓     |  ✓  | Wikis and knowledge bases. Sidebar and table of contents.       |
| `api`       |   ✓     |  ✓  | API reference. Sidebar and table of contents.                   |
| `blog`      |   —     |  —  | Blog posts. Shows the frontmatter `title`, `date` and `author`. |
| `changelog` |   —     |  —  | Changelogs. Shows the `title` and `date`.                       |

## Notes per layout

- **`page`** renders any [landing-page sections](/docs/authoring/landing-pages) from
  frontmatter, followed by the Markdown body. It's the default vault layout.
- **`docs`** is the richest layout: a left sidebar from your
  [navigation](/docs/navigation/sidebar), a right-hand table of contents built from the
  page headings, previous/next links, and the tab/version/language switchers when those
  features are enabled.
- **`blog`** and **`changelog`** read `date` (and `author` for blog) from
  [frontmatter](/docs/authoring/frontmatter) and render a clean, prose-focused article.

::: tip
Mix layouts within a single vault. Keep `docs` as the default, but give your landing
page `->layout('page')` and your release notes `->layout('changelog')`.
:::
