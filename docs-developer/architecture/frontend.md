---
title: Frontend pipeline
description: "Build setup, Inertia entry point, React layouts, blocks, markdown renderer, customizer, and diagnostics."
---

# Frontend pipeline

## Build and assets

- Vite runs **from the package root** (`vite.config.ts`).
- `hotFile: 'playground/public/hot'` — in development the playground's Blade root looks for this file to switch into HMR mode.
- Build output: `outDir: 'dist'`, with `base: '/vendor/plume/dist/'` so the published assets resolve correctly. The ServiceProvider publishes `dist/` to `public/vendor/plume/dist` under the tag `plume-assets`.
- `manualChunks` splits `react` and the `markdown` pipeline (unified/remark/rehype) for better caching.

## Root view — `resources/views/app.blade.php`

```blade
@plumeInertia        {{-- → <div id="app" data-page="..."></div> --}}
@plumeInertiaHead    {{-- → noop placeholder for future SSR / meta head --}}
```

If `public/hot` exists, Vite is loaded with `@viteReactRefresh`; otherwise the published build under `vendor/plume/dist` is used.

## React entry — `resources/js/app.tsx`

- `createInertiaApp` with `resolve` doing a glob over `./pages/**/*.tsx`.
- Reads `props.initialPage.props.plume` (shared from the controller) and applies the theme via `applyTheme` **before** mounting.
- The `Customizer` is lazy-loaded and only renders if the backend shared it (local environment).

## Layouts — `resources/js/pages/plume/`

Available: `docs`, `page`, `blog`, `api`, `wiki`, `changelog`. The component name is `plume/{layout}` and comes from `Page->getLayout() ?? $vault->getLayout()`.

### Typical structure — `docs.tsx`

```text
<DocsHeader ... tabs|versions|languages />
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
Custom block registration is described in [ADR-001](/dev-docs/adr/001-markdown-pages-blocks-system) but not yet implemented — see [Roadmap](/dev-docs/roadmap).
:::

## Markdown renderer — `components/plume/markdown-renderer.tsx`

Pipeline based on unified/remark/rehype:

- `remark-gfm`, `remark-frontmatter`, `remark-directive`.
- `remark-github-admonitions-to-directives` — supports both `:::` directives and GitHub-style `> [!TIP]` callouts.
- `rehype-raw`, `rehype-slug`, `rehype-external-links`.
- Code highlighting via Expressive Code (themes set by `code_theme_light/dark`).
- `rehypeContentAssets` — rewrites relative image URLs to use the page's `contentAssetBase` so assets resolve through the `/_content/{path}` route.

## Customizer (local only)

`components/customizer/customizer.tsx`. Posts to `/_plume/customizer` and `/_plume/customizer/reset` (routes from `routes/customizer.php`, only loaded in `local|testing`). It edits the **active vault's** `config.yml`.

## Diagnostics

In local/test, every vault exposes `GET {prefix}/_plume` → JSON with:

- The vault's class, prefix, absolute path, layout, discovery mode.
- Flags `hasNavigation/Tabs/Versions/Languages/Pages`.
- `allSlugs`.
- `pages[]` with `resolvedFilePath`, `fileExists`, `inNavigation`.
- All `routes[]` registered for this prefix.
