---
title: Images & Assets
description: Reference images and files that live alongside your Markdown content.
---
# Images &amp; Assets

Plume serves files that live next to your Markdown through a dedicated content asset  
route, so:

- You can keep images and downloads inside your content folder

- Assets are only visible when the request has access through the same authorization hook as the content.

## Relative references

Reference an asset relative to the current page and Plume resolves it for you:

```md
![Architecture diagram](./images/architecture.png)
```

Given a page at `content/docs/guides/deployment.md`, that image is expected at

`content/docs/guides/images/architecture.png`.

## How it works

Assets are served from a per-vault route:

```
/{prefix}/_content/{path}
```

For example, an image under `content/docs/guides/images/architecture.png` is served at

`/docs/_content/guides/images/architecture.png`. Plume rewrites relative references in

your Markdown to point at this route automatically, based on the page's directory.

::: tip

Keep assets close to the content that uses them — for example an `images/` folder

beside each page. This keeps relative links short and your content portable.

:::

::: note

The `_content` route lives under the vault prefix, so each vault serves its own assets

in isolation.

:::

## Light and dark images

A screenshot of a light interface glares on a dark page. Give each mode its image,
and end each address with the mode it is for, as on GitHub:

```md
![The release panel](images/release-panel.light.png#gh-light-mode-only)
![The release panel](images/release-panel.dark.png#gh-dark-mode-only)
```

Plume shows the first one in light mode and the second one in dark mode, and follows
the reader's switch. GitHub reads the same suffixes, so the Markdown file shows the
right image there too. An image without a suffix shows in both modes.

## Links between pages

Link to another page the way you would on GitHub, with the relative path of its
Markdown file. Plume points the link at the page:

```markdown
See [Deploying](deploy.md), or [the setup](../getting-started/installation.md#requirements).
```

`deploy.md` becomes the URL of the `deploy` page, resolved from the folder of the
current file; `index.md`, or `README.md`, becomes its folder's page. A file whose `slug` is overridden
in its frontmatter keeps its own URL, so link to it by that URL instead.

