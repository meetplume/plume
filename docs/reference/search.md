---
title: Search
description: Built-in full-text search, available per vault out of the box.
---

# Search

Every vault gets full-text search with no setup. The header includes a search button
(open it with the keyboard shortcut or by clicking), and Plume builds and serves a
search index for the vault automatically.

## How it works

For each vault, Plume exposes a search index as JSON at:

```
/{prefix}/_plume/search-index.json
```

The index covers the vault's pages, and the in-browser search dialog queries it
client-side — fast, and with no external service.

## Languages & versions

When a vault uses [languages](/docs/navigation/languages) or
[versions](/docs/navigation/versions), the search index is scoped to the active
language and version, so results match the edition the reader is currently viewing.
Plume passes the relevant `lang` and `version` query parameters for you.

::: note
Search is wired up by the `docs`, `wiki` and `api` layouts through their header. No
configuration is required — it works as soon as your vault has pages.
:::
