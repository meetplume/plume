---
title: Search
description: "How Plume builds, caches and serves a per-vault search index, and how the frontend consumes it."
---

# Search

Plume ships **client-side search** powered by [MiniSearch](https://github.com/lucaong/minisearch). The backend produces a per-vault JSON index; the frontend downloads it once, builds the search structure in the browser, and queries it locally. There is no server round-trip per keystroke.

## Backend

### `SearchIndexBuilder` — `src/Actions/SearchIndexBuilder.php`

Constructed with a `Vault` and optional `language` / `version`, it walks the vault's resolved navigation across all active tabs (plus any standalone `pages()`), de-duplicates by slug, skips hidden pages, and emits one record per page:

```text
{
  id, slug, title, description,
  headings: string[],   // h1–h3, code fences stripped
  body:     string,     // markdown stripped to plain text
  href:     string,     // absolute URL, prefix + lang/version/tab + slug
  group:    string|null // the NavGroup label
}
```

- **Title** comes from frontmatter `title`, else the first `#` heading, else the page label.
- **`signature()`** is a cheap fingerprint: it hashes each contributing file's resolved path + `mtime` **without reading contents**, so a cache layer can decide whether to rebuild by stat alone. It also hashes `INDEX_FORMAT`: bump that constant whenever a change to the builder alters the records for unchanged docs, or sites that update Plume keep serving the index built by the previous release.

### `SearchIndexCache` — `src/Actions/SearchIndexCache.php`

Wraps the builder and caches the built records keyed by the `signature()`, so the index is only rebuilt when a source file or the index format changes.

### `SearchIndexController` — `src/Http/Controllers/SearchIndexController.php`

Serves `GET {prefix}/_plume/search-index.json` (registered for every vault by the `VaultRouter`).

1. Resolves the vault from `vaultPrefix`; `abort` 404 if missing, 403 if `canAccess()` is false.
2. Resolves `lang` / `version` from the query string (validated against the vault; falls back to defaults; `400` on unknown values).
3. Reads the cached index and sends it as JSON with an `ETag` of the signature and `Cache-Control: public, max-age=300, must-revalidate`.
4. Honors `If-None-Match` → returns `304 Not Modified` when the client's ETag still matches.

So a vault with languages/versions has one index per `(language, version)` combination, negotiated via query params.

## Frontend

- `hooks/use-plume-search.ts` — fetches the index JSON for the current vault (passing `lang`/`version` when relevant), builds a **MiniSearch** instance over `title`, `headings`, `description` and `body`, and runs queries.
- `components/plume/search-button.tsx` — the header trigger; `hooks/use-search-shortcut.ts` binds the keyboard shortcut.
- `components/plume/search-dialog.tsx` — the modal that renders results and links to each `href`.
- `types/search.ts` — the record shape, kept in sync with `SearchIndexBuilder`'s output.

## Testing it

Hit the endpoint directly to inspect a vault's index:

```bash
curl https://playground.test/docs-contributing/_plume/search-index.json
```

Then verify the UI by opening the search dialog in the [playground](/docs-contributing/getting-started/playground) and checking that results link to the right pages.
</content>
