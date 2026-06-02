---
title: Frontend pipeline
description: "Build setup, Inertia entry point, React layouts, blocks, markdown renderer, search UI, customizer, and diagnostics."
---

# Frontend pipeline

## Build and assets

- Vite runs **from the package root** (`vite.config.ts`).
- `hotFile: 'playground/public/plume-hot'` — in development the playground's Blade root looks for this file to switch into HMR mode. Plume deliberately uses its **own** hot file (not `public/hot`) so the consuming app's Vite dev server never triggers Plume's dev mode by mistake (since 0.9.0).
- Build output: `outDir: 'dist'`, with `base: '/vendor/plume/dist/'` so the published assets resolve correctly. The ServiceProvider publishes `dist/` to `public/vendor/plume/dist` under the tag `plume-assets`.
- `manualChunks` splits `react` and the `markdown` pipeline (unified/remark/rehype) for better caching.

## Root view — `resources/views/app.blade.php`

```blade
@plumeInertia        {{-- → <div id="app" data-page="..."></div> --}}
@plumeInertiaHead    {{-- → noop placeholder for future SSR / meta head --}}
@plumeAssets         {{-- → Plume's isolated Vite tags (react-refresh + app.tsx) --}}
```

`@plumeAssets` is backed by `PlumeServiceProvider::renderAssets()`, which spins up a dedicated `Illuminate\Foundation\Vite` instance pinned to Plume's own hot file (`public/plume-hot`) and build directory (`vendor/plume/dist`). When Plume's dev server isn't running, it falls back to the published build automatically.

## React entry — `resources/js/app.tsx`

- `createInertiaApp` with `resolve` doing a glob over `./pages/**/*.tsx`.
- Reads `props.initialPage.props.plume` (shared from the controller) and applies the theme via `applyTheme` **before** mounting.
- The `Customizer` is lazy-loaded and only renders if the backend shared it (local environment).

## Layouts — `resources/js/pages/plume/`

Available: `docs`, `page`, `blog`, `api`, `wiki`, `changelog`. The component name is `plume/{layout}` and comes from `Page->getLayout() ?? $vault->getLayout()`.

### Typical structure — `docs.tsx`

```text
<DocsHeader ... tabs|versions|languages search />
  <SidebarNav navigation={...} />
  <main>
    {sections.map(Section)}     ← blocks from the frontmatter
    <MarkdownRenderer page={page} />
    <DocsFooter prev next />
  </main>
  <TableOfContents />
```

`page.tsx` is simpler — no sidebar, no TOC, just sections + markdown + footer.

## Blocks — `resources/js/components/blocks/`

Built-in: `hero1`, `hero2`, `features`, `call-to-action`, `header1`, `footer1`, `footer2`, `section` (the dispatcher). They render from the `sections` array carried in the page frontmatter (`meta.sections`).

::: note
Custom block registration is described in [ADR-001](/docs-contributing/adr/001-markdown-pages-blocks-system) but not yet implemented — see [Roadmap](/docs-contributing/roadmap).
:::

## Markdown renderer — `components/plume/markdown-renderer.tsx`

Pipeline based on unified/remark/rehype:

- `remark-gfm`, `remark-frontmatter`, `remark-directive`.
- `remark-github-admonitions-to-directives` — supports both `:::` directives and GitHub-style `> [!TIP]` callouts (see [Callouts](/docs-contributing/authoring/callouts)).
- `rehype-raw`, `rehype-slug`, `rehype-external-links`.
- Code highlighting via Expressive Code (themes set by `code_theme_light/dark`).
- `rehypeContentAssets` — rewrites relative image URLs to use the page's `contentAssetBase` so assets resolve through the `/_content/{path}` route.

## Search UI — `components/plume/search-*` + `hooks/`

- `search-button.tsx` — the trigger in `DocsHeader`, with a keyboard shortcut wired by `use-search-shortcut.ts`.
- `search-dialog.tsx` — the modal results view.
- `use-plume-search.ts` — fetches `{prefix}/_plume/search-index.json`, builds a **MiniSearch** index client-side, and runs queries.
- `types/search.ts` — the record shape shared with the backend.

The index payload and caching are produced by the PHP side — see [Search](/docs-contributing/architecture/search).

## Customizer (local only)

`components/customizer/customizer.tsx`. Posts to `/_plume/customizer` and `/_plume/customizer/reset` (routes from `routes/customizer.php`, only loaded in `local|testing`). It edits the **active vault's** `config.yml`.

## Diagnostics

In local/test, every vault exposes `GET {prefix}/_plume` → JSON with:

- The vault's class, prefix, relative + absolute path, layout, discovery mode.
- Flags `hasNavigation/Tabs/Versions/Languages/Pages`.
- `allSlugs`.
- `pages[]` with `resolvedFilePath`, `fileExists`, `hidden`, `order`, `inNavigation`.
- All `routes[]` registered for this prefix.
</content>
