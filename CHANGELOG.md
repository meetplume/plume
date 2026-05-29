# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

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
