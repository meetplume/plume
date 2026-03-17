# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

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
