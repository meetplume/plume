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