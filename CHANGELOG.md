# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)

and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [0.11.2] - 2026-10-21

### Added

- In Auto and Mapped discovery, a folder's `README.md` is its page when the folder has no `index.md`, as on GitHub: the root one is the vault's home. A folder of docs browsed on GitHub, with its `README.md` as table of contents, is served as it is. Links to a `README.md` point to its folder.

### Changed

- A subfolder's `index.md` is served at the folder's address (`/docs/guides`, was `/docs/guides/index`), where links to it already pointed, and opens its group.

## [0.11.1] - 2026-10-19

### Fixed

- Route names use dots where a vault's prefix or a page key has slashes: `plume.docs.users.root` instead of `plume.docs/users.root`. Tools that turn route names into code, such as Wayfinder, generated invalid TypeScript from them. A `route()` call on an old name needs the new one.

## [0.11.0] - 2026-10-18

### Added

- Relative links to Markdown files (`setup.md`, `../guides/deploy.md#step-2`), as written for GitHub, point to their page, resolved from the folder of the current file.
- `Vault::languageFor()`: let the app pick the language of each request (a per-tenant setting, a user preference). The vault then serves it with no language switcher and no language segment in routes or links, and fills in the pages it lacks from the default language.
- Plume's interface (search, "On this page", menus, the copy button of code blocks) follows the language of the page, through Laravel JSON translations. French, Portuguese and Spanish ship with Plume; the host app overrides any key in its own `lang/{locale}.json`. The page's `<html lang>` follows too.
- A language menu in the header, also next to the light/dark switch of the mobile menu (versions get the same), replacing the native selects on a row of their own. Each language links to the same page in that language, or to its home while the page is not translated, through an Inertia visit.
- `Plume::configure()->poweredBy(false)` leaves the *Powered by Plume* credit out of the docs footer.
- Page menu next to the page title: copy the page as Markdown, view it as Markdown, open it in ChatGPT or Claude, or export it as a PDF through the browser's print dialog. Every page is also served as Markdown at its address followed by `.md`, through the same middleware and `canAccess()` as the page. `Vault::pageActions()` picks the entries; return `PageAction::internal()` for a vault behind a login, or `[]` to leave the menu out. Its words, and the question put to ChatGPT and Claude, follow the language of the page. See the Page Actions docs.
- `Vault::middleware()`: the route middleware of a vault's pages, search index and content assets (`['web']` by default). Add `auth` to send guests to the login page instead of a 403, or a middleware that keeps the vault off some hosts.
- `Vault::canSeePage()`: show some pages to some readers only, from their key and frontmatter (say, the feature or permission they document). Hidden pages are left out of the sidebar, search, previous/next links and tabs, and answer 404.
- `group_label` frontmatter key: the label of the page's group in Auto discovery, instead of the title-cased folder name (`etudes-de-rentabilite` read "Etudes De Rentabilite").
- `appearanceKey()` on the configuration: the browser storage key (localStorage and cookie) of the light/dark choice, `theme` by default. Give it the host app's own key to share one choice; a stored `system` follows the OS. The choice now applies before the first paint.
- A `head` partial: publish `resources/views/vendor/plume/head.blade.php` to add styles, a favicon or scripts to every Plume page, after Plume's stylesheet.
- Print styles: a printed page keeps only the article, in light mode whatever the screen shows, at full width, without splitting a callout, code block, table or figure across pages.
- A `brand` partial: publish `resources/views/vendor/plume/brand.blade.php` to replace the logo and the site name of the header with any markup (a logo in a colored square, a second line, a badge).
- `name()` takes a closure, called on each request with the request: a site name that depends on it, such as the tenant of a multi-tenant app.
- A vault's `backLink()` returns a link back to the app it lives in, shown at the bottom of the sidebar and of the menu on small screens.
- `?embed` shows a page's content alone, for an app to open it in a drawer or a dialog through an iframe. Its text uses the compact type scale of a side panel; `&scale=base` or `&scale=lg` picks a larger one. Inside the frame, the vault's pages open in place without filling the browser's history, other pages of the site open in the whole window, and each page posts its title and address to the app. See the Embedding docs.

### Changed

- The previous and next links under a page are cards, labelled *Previous* and *Next* (translated), with the page title below. They open their page through an Inertia visit, as the sidebar does, instead of reloading the whole page.
- Apply the rules Rector 2.7 adds (`??=`, `__DIR__` paths), so `composer test` passes again with current dependencies.

### Fixed

- The `test:type-coverage` script now fails when type coverage drops below 100%. It passed `--exactly=100`, which the type coverage plugin ignores, so it always exited `0`; it now uses `--min=100`. The untyped parameters this let through are typed: two closures in `DiagnosticsController`, and `PlumeInertiaResponse::toResponse()`, only reported with the lowest dependencies.
- With languages, Auto and Mapped discovery served no page at all: slugs were collected from the vault root (`en/guide`) instead of each language folder. Each language home (`/docs/pt`) is now routed too.
- In a vault with languages, relative images of a page pointed outside its language folder (`/docs/_content/guides/shot.png` for `pt/guides/deploy.md`) and answered 404.
- `Language::slugs()` was documented but never applied: translated slugs now drive the routes, the sidebar, the search results and the language menu.
- Below the `lg` breakpoint the sidebar was hidden and the mobile menu did not list the navigation: no other page could be reached on a phone. The mobile menu now lists the sidebar's groups and pages, and closes on navigation.
- Inside an Inertia app, every visit to a Plume page answered 409 and reloaded the whole page: the host's Inertia middleware compared its asset version with Plume's. Vault routes now leave the host's Inertia middleware out.
- Auto discovery showed the root group as "_Root" in the sidebar, and its pages under that label in search results; it is now unlabelled.
- Pages had no doctype and rendered in quirks mode.
- The tab title ended with "Laravel" (`VITE_APP_NAME` at Plume's build) instead of the site name set by `Plume::configure()->name()`.
- Search snippets (Cmd-K) no longer show raw callout syntax such as `:::tip[Title]`, `::: warning Title`, `:::` or `> [!TIP]`: the markers are dropped, the callout title and text stay searchable. Code fences written with tildes or nested in a longer fence are now left out of the index like other code blocks.
- Search snippets (Cmd-K) no longer show raw table syntax (`| | |`, `|---|:---:|`): the delimiter row and the pipes are dropped, the cells keep their text. Inline code now keeps its text in the index instead of leaving a gap, so a search for `npm run build` finds the page that mentions it.
- The search index is rebuilt after a Plume update that changes how it is built. The cache in `storage/app/plume/search` and the `ETag` used to depend on the docs only, so an existing site kept serving the old index until a page changed; they now include the index format, and this release bumps it so the snippet fixes above apply without clearing anything.
- On a long page, the table of contents scrolls in its own panel, but the active entries left it as the reader went on. The panel now follows them, unless the pointer is over it.
- The Vite dev server no longer loads every Lucide icon on each page of the playground. Pre-bundled next to `lucide-react`, `lucide-react/dynamicIconImports` turned each of the 1,669 icons into a chunk that every page loaded: 1,841 requests and a first paint after 1.1 s. It is now left out of pre-bundling, so a page loads the icons it shows (181 requests, first paint at 0.3 s). Built assets are unchanged.
- Pages no longer load every Shiki code theme. The theme customizer was mounted even where it is disabled, production included, and loaded about 60 themes for its previews on each full page load. It is now loaded only where it is enabled, and loads the previews when its panel opens. In production, a cold page load goes from 102 JavaScript files to 25; in the playground, the content shows 420–520 ms sooner on a throttled connection (4G, CPU ×4).
- Changing page no longer leaves the article blank while its code blocks are highlighted. The page paints its text first, then its highlighted code: on a throttled connection (4G, CPU ×4), the text of the next page shows in 110–150 ms instead of 170–680 ms, and on first load as soon as the layout (0.9 s instead of 2.2–2.7 s). The Expressive Code engine is also built once per pair of code themes instead of on every page.
- A page without a frontmatter `title` took its tab title and sidebar label from its file name (`acces-mobile` read "Acces Mobile"), while search used its first `# ` heading. Both now use that heading.
- In Auto discovery, the vault home (its root `index.md`) was sorted by name among the other root pages, often mid-list. It now opens its group.
- The logo and the home link of the header were ignored unless `header()` was configured: the header showed the site name, linking to `/`.
- An address with an anchor (`/docs/guide#install`) opened the page at the top: the browser looked for the anchor before the page's text was rendered. The page now scrolls to it once its text is in.
- The copy button of code blocks had a border, and square corners in a theme with a radius of 8px or less. It now has no border, and the radius of a button (`--radius` minus 2px).
- The sidebar and the table of contents stuck at a fixed 65px from the top: under a taller header (a logo on two lines, a row of tabs) they slid beneath it while scrolling. The header now measures itself (`--plume-header-height`), and headings scroll to just below it too.
- Inside a tip, info, note, warning or danger callout, bold text, links, list markers and headings kept the prose colors (dark gray on a green tip). They now take the callout's color; inline code keeps its own.

### Security

- The search index, which holds the text of every page, is sent `Cache-Control: private` instead of `public`, so a shared cache never keeps the index of a vault gated by `canAccess()`.
- The theme customizer routes, loaded in `local` and `testing`, had no middleware at all (no session, no CSRF check, on every host) and write a vault's `config.yml`. They now live under each vault (`/{prefix}/_plume/customizer`), behind its middleware and `canAccess()`.

## [0.10.5] - 2026-06-02

### Added

- `plume:update-assets` Artisan command that removes the previously published assets at `public/vendor/plume` and republishes the current ones, so stale hashed files don't linger after a package update. Run it after `composer install`/`composer update` in your deploy script. See the Installation docs.

## [0.10.4] - 2026-06-02

### Fixed

- Search palette (Cmd-K) could keep serving a stale, smaller index after new docs were added. The client cached the index in `sessionStorage` and never revalidated; it now revalidates against the server with `If-None-Match` (using the stored ETag) and replaces the cached index on a `200`, while still serving the cache instantly and keeping it on a `304` or network error.

## [0.10.3] - 2026-05-31

### Changed

- Adds tests on release, with a concurrency check

## [0.10.2] - 2026-05-29

### Changed

- Adjust scaffolding.

## [0.10.1] - 2026-05-29

### Changed

- Fix path return type.

## [0.10.0] - 2026-05-29

### Added

- `plume:vault {name}` Artisan command that scaffolds a starter vault for quick onboarding: generates an `App\Plume\{Name}Vault` class (with `Discovery::Auto`) and a `content/{name}/index.md` index page.
- Docs-guide update.
- Docs sync GH workflow triggers website deployment.
- Docs sync GH workflow also publishes the playground's `DocsGuideVault.php` to meetplume.com as the example vault class.

### Changed

- `Vault::getAbsolutePath()` now resolves the path through `getPath()` instead of reading the `$path` property directly, so subclasses can override path resolution.

## [0.9.2] - 2026-05-29

- Register vault routes in console too, so they show in `route:list` and can be cached with `route:cache` (routes use controllers, not closures, so they are fully cacheable).
- Update git attributes for export-ignore.

## [0.9.1] - 2026-05-29

- Have same prefix in playground and website, for docs vault.

## [0.9.0] - 2026-05-29

### Fixed

- Asset loading collision when the consuming Laravel app runs its own Vite dev server. Plume now uses a dedicated `public/plume-hot` file and an isolated `Illuminate\Foundation\Vite` instance via a new `@plumeAssets` Blade directive, so the consuming app's `public/hot` no longer triggers Plume's dev mode by mistake. The package falls back to the published `vendor/plume/dist` assets whenever Plume's own dev server is not running.

### Changed

- Documentation reorganized into two vaults: a user-facing `docs-guide` with getting-started and usage sections, and `docs-contributing`, for package developers.
- Bumped GitHub Actions to Node.js 24-based versions: `actions/checkout@v5`, `actions/setup-node@v5`, `actions/cache@v5`.
- CI: added a `sync-docs` workflow that publishes documentation to meetplume.com
- Removed the standalone `playground` workflow.

## [0.8.0] - 2026-05-28

### Added

- Client-side search powered by MiniSearch, with search button and dialog in `DocsHeader`, keyboard shortcut, and a `SearchIndexController` serving a cached per-vault index.
- Developer docs vault: reorganized `docs/` into `docs-developer/` with architecture, getting-started, authoring, and roadmap sections, plus `PlumeDeveloperDocsVault`.

## [0.7.1] - 2026-03-26

- Add web middleware to Plume routes.

## [0.7.0] - 2026-03-17

- In PlumeServiceProvider, only run configuration if running in console.

## [0.6.0] - 2026-03-17

### Added

- **Built-in Inertia replacement** — `PlumeInertia` and `PlumeInertiaResponse` classes replace the `inertiajs/inertia-laravel` dependency. The package now handles its own page rendering with `@plumeInertia` and `@plumeInertiaHead` Blade directives.
- `Page::home()` method as a convenient alias for `->slug('/')`.
- `Page::route()` method for registering pages at absolute routes outside the vault prefix.
- `Page::layout()` method for per-page layout override (falls back to the vault layout).
- `Vault::getHome()` method and `$home` property for configurable home URL in headers.
- `homeUrl` prop in `Header1` and `DocsHeader` React components, linking the logo to the vault's home URL.
- Absolute route registration in `VaultRouter` for pages with a custom `route()`.
- `orchestra/testbench` for package-level testing with full Laravel environment. Tests reorganized into `Unit/` and `Feature/` suites.

### Changed

- Removed `inertiajs/inertia-laravel` as a dependency — the package no longer requires Inertia.
- Pages with a custom `route()` are excluded from the vault's slug collection to avoid duplicate route registration.

## [0.5.0] - 2026-03-16

### Added

- **Vault system** replacing Collections. Vaults are dedicated PHP classes with support for navigation, tabs, versions, languages, and extra pages.
- `VaultRouter` for automatic route registration with support for language, version, and tab URL segments.
- `VaultPageController` replacing `PageController`, with full support for tabs, versions, languages, and prev/next navigation.
- Fluent `Plume::configure()` API via new `PlumeConfiguration` class for global setup (name, theme, header, footer, vaults).
- New domain classes: `Header`, `Footer`, `FooterColumn`, `Social`, `Tab`, `Version`, `Language`.
- `Page` class replacing `PageItem`, with `path()`, `order()`, `slug()`, `label()`, `hidden()` methods.
- Multiple Inertia page layouts: `docs`, `api`, `blog`, `changelog`, `wiki`, and `page`.
- Prev/next page navigation in docs footer.
- Header sub-navigation with tab, version selector, and language selector dropdowns.
- Vaults are private by default — each vault must override `canAccess()` to grant access.
- Root/index route support — vaults can declare a page with `->slug('/')` to serve a root route.
- Diagnostics system (local/test only): JSON endpoint at `/{prefix}/_plume` with vault introspection, plus a Diagnostics panel in the Customizer.
- `Discovery` enum with three modes: `Manual`, `Mapped`, and `Auto`.
- `FilesystemScanner` for recursive `.md` file discovery with frontmatter parsing and auto-generated `NavGroup` structures.
- `DocsHeader` and `DocsFooter` React components with logo, nav links, social icons, dark mode toggle, and mobile menu.
- `Vault::pages()` method for declaring routable pages that don't appear in sidebar navigation.

### Changed

- Routes now use a single parameterized route per vault instead of per-page route registration.
- Route defaults pass string identifiers instead of object instances, improving route caching compatibility.
- Customizer theme configuration is now per-vault instead of global.
- Playground added to CI test suite with asset building and full setup.

### Removed

- `Collection` class — replaced by `Vault`.
- `PageItem` class — replaced by `Page`.
- `PageController` — replaced by `VaultPageController`.
- `Plume::config()` and `Plume::collection()` facade methods.
- Global `configPath` — config is now per-vault via `config.yml` in each vault's content directory.

### Fixed

- Routing: parameterized routes resolve serialization and caching issues.
- TypeScript type safety in `rehypeContentAssets` (HAST types), icon hook operator precedence, and import ordering.

## [0.4.0] - 2026-03-16

- Triggered a release by mistake.

## [0.3.4] - 2026-02-27

- We may present Image URL's, preserving them in the content root directory.
- Try deploy hook.
- Optimize asset bundle.

## [0.3.2] - 2026-02-27

- Docs are shipped.
- Release system

## [0.2.0] - 2026-02-27

### Added

- Block components: Hero1, Hero2, Footer1, Footer2, Features, CallToAction.
- Plume Icon.

## [0.1.0] - 2026-01-26

### Added

- Initial release of Plume package with playground application for testing.
- 2 sample pages in the playground demonstrating basic content rendering.

