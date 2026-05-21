# ADR-001: Markdown pages, blocks, and navigation system

**Date:** 2026-03-16
**Status:** In discussion

## Context

Plume needs to evolve into a system where writing Markdown files produces full pages delivered to the frontend. Pages are composed of blocks with dependencies (navigation, shared data, etc.).

Today Plume already handles the fundamental pipeline (Markdown + frontmatter → Inertia → React), but configuration is scattered across PHP files. The goal is a **PHP-first, Filament-inspired architecture** where Vault classes are the unit of organization, and complexity is unlocked progressively by adding methods — not flags or config switches.

---

## 1. Content structure — Vaults

Each vault is a content scope with its own layout and behavior:

| Layout        | Behavior                          | Navigation          | Ordering            | Extras                           |
|---------------|-----------------------------------|---------------------|---------------------|----------------------------------|
| **page**      | Free-form blocks from frontmatter | Global menu         | Manual (order)      | —                                |
| **docs**      | Sidebar + TOC                     | Hierarchical tree   | Manual (order/group) | Search, breadcrumbs, prev/next  |
| **blog**      | Post + listing                    | Chronological       | By date             | Tags, RSS, pagination           |
| **api**       | API reference                     | By resource         | Manual              | OpenAPI integration              |
| **changelog** | Chronological entries             | By date             | By date             | —                                |
| **wiki**      | Content + sidebar                 | Flat or categories  | Alphabetical        | Search                           |

## 2. PHP-first architecture — Filament-inspired

### 2.1 The core principle: methods you define unlock features

```
languages() → versions() → tabs() → navigation() → pages
    ↓             ↓           ↓           ↓            ↓
  optional     optional    optional    required     leaf nodes
```

Each undefined layer collapses. The URL structure adapts automatically:

```
FULL:    /{prefix}/{lang}/{version}/{tab}/{group_path}/{page}
NO TAB:  /{prefix}/{lang}/{version}/{group_path}/{page}
NO VER:  /{prefix}/{lang}/{group_path}/{page}
NO LANG: /{prefix}/{group_path}/{page}
FLAT:    /{prefix}/{page}
```

### 2.2 Level 0 — Inline (playground, quick experiments)

```php
// AppServiceProvider — no class needed
public function boot(): void
{
    Plume::pages(base_path('content'));
    // Done. Every .md file in content/ becomes a page.
    // index.md → /, about.md → /about, pricing.md → /pricing
}
```

### 2.3 Level 1 — Vault class (the standard way)

```php
// app/Plume/DocsVault.php

class DocsVault extends Vault
{
    protected string $prefix = '/docs';
    protected string $path = 'content/docs';
    protected string $layout = 'docs';

    public function navigation(): array
    {
        return [
            NavGroup::make('getting-started')
                ->icon('rocket')
                ->pages([
                    Page::make('intro'),
                    Page::make('quickstart'),
                    Page::make('installation'),
                ]),

            NavGroup::make('configuration')
                ->icon('settings')
                ->pages([
                    Page::make('config/basic'),
                    Page::make('config/advanced'),
                ]),
        ];
    }
}
```

Registration — Filament style:

```php
// AppServiceProvider
public function boot(): void
{
    Plume::vaults([
        DocsVault::class,
        BlogVault::class,
    ]);
}
```

URL: `/docs/quickstart`, `/docs/config/basic`

### 2.4 Level 2 — Add tabs

**The toggle:** define `tabs()` instead of (or wrapping) `navigation()`.

```php
class DocsVault extends Vault
{
    protected string $prefix = '/docs';
    protected string $path = 'content/docs';
    protected string $layout = 'docs';

    public function tabs(): array
    {
        return [
            Tab::make('documentation')
                ->label('Documentation')
                ->icon('book')
                ->groups([
                    NavGroup::make('getting-started')
                        ->pages([Page::make('intro'), Page::make('quickstart')]),
                    NavGroup::make('configuration')
                        ->pages([Page::make('config/basic'), Page::make('config/advanced')]),
                ]),

            Tab::make('api')
                ->label('API Reference')
                ->icon('code')
                ->groups([
                    NavGroup::make('authentication')
                        ->pages([Page::make('api/overview'), Page::make('api/tokens')]),
                    NavGroup::make('endpoints')
                        ->pages([Page::make('api/users'), Page::make('api/projects')]),
                ]),

            Tab::make('changelog')
                ->label('Changelog')
                ->icon('clock')
                ->groups([
                    NavGroup::make('changelog')
                        ->pages([Page::make('changelog')]),
                ]),
        ];
    }
}
```

URL: `/docs/documentation/quickstart`, `/docs/api/users`

The sidebar changes per tab. The tab selector appears in the header or as a sub-nav.

### 2.5 Level 3 — Add versions

**The toggle:** define `versions()`.

```php
class DocsVault extends Vault
{
    protected string $prefix = '/docs';
    protected string $path = 'content/docs';
    protected string $layout = 'docs';

    public function versions(): array
    {
        return [
            Version::make('v2')->default(),
            Version::make('v1'),
        ];
    }

    public function tabs(): array
    {
        return [
            Tab::make('documentation')->groups([/* ... */]),
            Tab::make('api')->groups([/* ... */]),
        ];
    }
}
```

URL: `/docs/v2/documentation/quickstart` (default version can be prefixless)
URL: `/docs/v1/documentation/quickstart`

Content resolves: `content/docs/v2/quickstart.md` vs `content/docs/v1/quickstart.md`

**When versions need different navigation** — override directly on the Version:

```php
public function versions(): array
{
    return [
        Version::make('v2')->default()
            ->tabs([
                Tab::make('documentation')->groups([/* ... */]),
                Tab::make('api')->groups([/* ... */]),       // v2 has API docs
            ]),

        Version::make('v1')
            ->tabs([
                Tab::make('documentation')->groups([/* ... */]),
                // v1 had no API tab
            ]),
    ];
}
```

If a version doesn't define its own tabs/navigation, it inherits from the vault.

### 2.6 Level 4 — Add languages

**The toggle:** define `languages()`.

```php
class DocsVault extends Vault
{
    protected string $prefix = '/docs';
    protected string $path = 'content/docs';
    protected string $layout = 'docs';

    public function languages(): array
    {
        return [
            Language::make('en', 'English')->default(),
            Language::make('pt', 'Português'),
            Language::make('fr', 'Français'),
        ];
    }

    public function versions(): array { ... }
    public function tabs(): array { ... }
}
```

URL: `/docs/en/v2/documentation/quickstart` (default lang can be prefixless)
URL: `/docs/pt/v2/documentation/quickstart`

Content: `content/docs/en/quickstart.md`, `content/docs/pt/quickstart.md`

**Navigation structure is shared** by default — same groups and pages, different content files per language folder. When a language needs custom slugs (translated URLs):

```php
Language::make('fr', 'Français')
    ->slugs([
        'quickstart' => 'demarrage-rapide',
        'contact' => 'contactez-nous',
    ]),
```

URL: `/docs/fr/v2/documentation/demarrage-rapide` → resolves `content/docs/fr/quickstart.md`

### 2.7 Filament-style extraction to classes

When navigation gets large, extract to separate classes — like Filament Resources have separate Page classes:

```
app/Plume/
├── DocsVault.php
├── DocsVault/
│   ├── DocumentationTab.php
│   ├── ApiReferenceTab.php
│   └── V1Navigation.php
├── BlogVault.php
└── PagesVault.php
```

The vault references them:

```php
class DocsVault extends Vault
{
    public function tabs(): array
    {
        return [
            DocumentationTab::make(),
            ApiReferenceTab::make(),
        ];
    }
}
```

Each Tab class is self-contained:

```php
// app/Plume/DocsVault/DocumentationTab.php

class DocumentationTab extends Tab
{
    protected string $key = 'documentation';
    protected string $label = 'Documentation';
    protected string $icon = 'book';

    public function groups(): array
    {
        return [
            NavGroup::make('getting-started')
                ->icon('rocket')
                ->pages([
                    Page::make('intro')->label('Introduction'),
                    Page::make('quickstart'),
                    Page::make('installation'),
                ]),

            NavGroup::make('configuration')
                ->icon('settings')
                ->pages([
                    Page::make('config/basic')->label('Basic'),
                    Page::make('config/advanced')->label('Advanced'),
                    Page::make('config/themes'),
                ]),
        ];
    }
}
```

### 2.8 Global configuration — PlumeServiceProvider

Like Filament's `AdminPanelProvider`:

```php
class PlumeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Plume::configure()
            ->name('My Project')
            ->url('https://myproject.com')
            ->logo('/logo-light.svg', '/logo-dark.svg')
            ->favicon('/favicon.ico')
            ->theme('ocean')

            ->header(
                Header::make('header1')
                    ->cta('Get Started', '/docs/quickstart')
                    ->socials([
                        Social::github('https://github.com/myproject'),
                        Social::x('https://x.com/myproject'),
                    ])
            )

            ->footer(
                Footer::make('footer1')
                    ->columns([
                        FooterColumn::make('Product')->links([...]),
                        FooterColumn::make('Resources')->links([...]),
                    ])
            )

            ->vaults([
                PagesVault::class,
                DocsVault::class,
                BlogVault::class,
            ]);
    }
}
```

### 2.9 Complete picture

```
PlumeServiceProvider (global: name, theme, header, footer)
    │
    ├── PagesVault (layout: page)
    │     └── routes: / → index.md, /pricing → pricing.md
    │
    ├── DocsVault (layout: docs)
    │     ├── languages()  ← optional layer
    │     ├── versions()   ← optional layer
    │     ├── tabs()       ← optional layer
    │     │     ├── DocumentationTab
    │     │     │     └── groups → pages
    │     │     └── ApiReferenceTab
    │     │           └── groups → pages
    │     └── navigation() ← fallback if no tabs
    │           └── groups → pages
    │
    └── BlogVault (layout: blog)
          └── auto-discovered from files, sorted by date
```

### 2.10 Resolution order

The vault resolves the active navigation by walking the hierarchy:

1. Has `languages()`? → match lang from URL, prefix URLs with lang
2. Has `versions()`? → match version from URL, prefix URLs with version
3. Version defines its own `tabs()`? → use those. Otherwise, vault's `tabs()`?
4. Has `tabs()`? → match tab from URL, use tab's groups for sidebar
5. Fall back to `navigation()` → use directly as sidebar groups

Each layer is **independent and composable**. No flags, no booleans — the presence of the method IS the toggle.

## 3. Block dependencies

Cross-cutting dependencies that blocks and pages need:

- **SEO / Meta** — `title`, `description`, `og:image`, `canonical`. Smart fallbacks (H1, first 160 chars, etc.)
- **Breadcrumbs** — derived from navigation structure (docs, wiki)
- **Prev/Next** — derived from navigation order (docs, blog)
- **Social links** — shared between header and footer, defined once
- **Logo / Branding** — defined once at vault or global level
- **Global CTA** — a CTA that appears across pages (top banner, footer)
- **Announcement bar** — temporary banner at the top of the site
- **Favicon** — site icon
- **Fonts** — custom typography
- **Scripts** — analytics, custom JS injection
- **Sitemap** — auto-generated
- **RSS/Atom** — for blog vaults
- **Search** — configuration and provider
- **404 page** — custom not found
- **Robots.txt** — search engine directives
- **Draft status** — unpublished pages via frontmatter

## 4. `@` syntax — Plume directive convention

**Decision:** use `@` as Plume's universal marker, in two syntactic positions that never collide.

### Why `@`

- **1 character** — minimum possible, fast to type
- **No conflicts** with Markdown, YAML, or programming language syntax
- **Familiar** — evokes decorators (`@route`), mentions (`@user`), directives (`@include` in Blade/C)
- **LLM-friendly** — appears in patterns LLMs master (fenced blocks, links)
- **Visually distinct** — `` ```@hero `` never confuses with `` ```php ``

### 4.1 Blocks — `@` in the fence of a code block

For blocks with YAML props and markdown slots. Three variants:

**Simple props (no slot):**

````markdown
```@include
src: shared/requirements.md
```

```@code
src: app/Models/User.php
lines: 10-35
title: User.php
highlight: [15, 20-25]
```
````

**Complex props with arrays:**

````markdown
```@features
title: Why choose us
columns: 3
features:
  - title: Fast
    icon: Zap
    description: Blazing fast performance.
  - title: Secure
    icon: Shield
    description: Built with security in mind.
```
````

**Props + markdown slot (separated by `---`):**

````markdown
```@hero
title: Welcome to Plume
links:
  - label: Get Started
    href: /docs
---
This is the **slot content** — rich markdown rendered inside the hero.

Supports [links](/docs), `code`, images, everything.
```
````

**Named slots (for blocks with multiple content zones):**

````markdown
```@hero
title: Welcome
---
#default
The main content goes here with **rich markdown**.

#aside
A secondary piece of content, like a code example or image.
```
````

PHP splits by `---`: above is YAML (props), below is markdown (slots). `#name` markers separate named slots.

### 4.2 Inline — `@` as URL prefix inside Markdown links

For inline references (tooltips, popups, glossary). Uses standard Markdown link syntax:

```markdown
The [Plume](@glossary/plume) package transforms Markdown into pages.
```

The `@` at the start of the URL tells Plume it's an internal reference, not a real link.

**Three complexity levels (progressive disclosure):**

**Level 1 — Direct path, zero config:**
```markdown
The [Plume](@glossary/plume) package transforms Markdown into pages.
```
→ PHP resolves `content/glossary/plume.md` and sends as popup content.

**Level 2 — Few params, query string:**
```markdown
The [Plume](@glossary/plume?mode=modal&width=lg) package transforms Markdown.
```

**Level 3 — Complex props, `@ref` block:**

Inline stays clean, definition goes to a separate block (inspired by Markdown reference-style links):

````markdown
The [Plume](@plume) package uses [Inertia](@inertia) for server/client communication.

```@ref plume
src: glossary/plume.md
mode: modal
width: lg
trigger: hover
```

```@ref inertia
src: glossary/inertia.md
mode: tooltip
position: above
```
````

If a `@ref id` block exists, use those props. If not, `@id` is treated as a direct path (level 1).

### 4.3 Convention summary

| Context | Syntax | LLM writes... |
|---|---|---|
| Block | `` ```@hero `` | Fenced block with YAML (masters it) |
| Block with slot | `` ```@hero `` + `---` + markdown | YAML + markdown (masters it) |
| Inline simple | `[text](@path)` | Markdown link (masters it) |
| Inline with params | `[text](@path?k=v)` | Link with query params (masters it) |
| Inline complex | `[text](@id)` + `` ```@ref id `` | Link + separate block (masters it) |

Positions are never ambiguous:
- Inside `` ``` `` → Plume block
- Inside `()` of a link → Plume reference
- Loose in text → doesn't exist, never happens

## 5. Markdown composition (server-side preprocessing in PHP)

Preprocessing layer between file read and frontend delivery:

```
.md file → PHP preprocessor → processed markdown → Inertia → React
                ↓
          1. Frontmatter extraction
          2. Include resolution
          3. Code embedding
          4. Variable interpolation
          5. Inline reference resolution
```

### 5.1 Includes

````markdown
## Getting Started

```@include
src: shared/requirements.md
```

Once you have the prerequisites:

```@include
src: shared/install-steps.md
```
````

Recursive resolution in PHP, with cycle protection (`maxDepth: 5`). Enables reusable partials — an install snippet, a legal notice, a CTA, used across 20 pages. Edit once, updates everywhere.

### 5.2 Code embedding

For documentation that stays in sync with real source code:

````markdown
Here's how we define the User model:

```@code
src: app/Models/User.php
lines: 10-35
lang: php
title: User.php
```
````

PHP reads the file, extracts the lines, and generates a normal fenced code block. React doesn't need to know it came from a file.

Supported options:
- `lines: 10-35` — line range
- `highlight: [15, 20-25]` — highlighted lines (Expressive Code)
- `collapse: 1-8` — collapsible lines
- `title: User.php` — block title
- `from: "// START"` / `to: "// END"` — extract by code markers (more robust than line numbers)

### 5.3 Variable interpolation

```markdown
Welcome to {{ site.name }}! Current version: {{ version }}.
```

Variable sources (by precedence):
1. Page frontmatter
2. Vault shared data
3. Global Plume config
4. Data files in content directory

## 6. Custom Blocks

### 6.1 Registration

**Convention-based** — Plume discovers blocks in a directory:
```
resources/js/components/blocks/
├── hero1.tsx          ← built-in
├── pricing.tsx        ← user custom
└── testimonials.tsx   ← user custom
```

**Explicit via Plume config:**
```php
Plume::configure()
    ->blocks([
        'pricing' => PricingBlock::class,    // optional PHP resolver
        'team' => TeamBlock::class,
    ]);
```

### 6.2 Extensible dispatcher

Instead of the current hardcoded switch, a dynamic registry:

```tsx
// block-registry.ts
const builtInBlocks = {
    hero1: () => import('./hero1'),
    hero2: () => import('./hero2'),
    features: () => import('./features'),
    callToAction: () => import('./call-to-action'),
};

const customBlocks = {
    pricing: () => import('@/components/blocks/pricing'),
    team: () => import('@/components/blocks/team'),
};

const blocks = { ...builtInBlocks, ...customBlocks };
```

### 6.3 Data resolution

Three patterns for feeding data to blocks:

**Inline in frontmatter** (already works today):
```yaml
sections:
  - type: features
    features:
      - title: Fast
        icon: Zap
```

**External data file** (avoids giant frontmatter):
```yaml
sections:
  - type: pricing
    data: data/pricing.yml
```

**PHP resolver** (dynamic or computed data):
```php
class RecentPostsResolver implements BlockResolver
{
    public function resolve(array $props, Vault $vault): array
    {
        return [
            ...$props,
            'posts' => Plume::vault('blog')->latestPages(limit: $props['limit'] ?? 3),
        ];
    }
}
```

## 7. Complete pipeline

```
                    PHP (server)                          React (client)
                    ────────────                          ──────────────
.md file ──→ Read file
             ├─→ Extract frontmatter (YAML)
             ├─→ Preprocess markdown body:
             │     ├─ Resolve ```@include blocks
             │     ├─ Resolve ```@code blocks
             │     ├─ Interpolate {{ variables }}
             │     └─ Resolve [text](@ref) inline refs
             ├─→ Resolve block data:
             │     ├─ Load data files (data: pricing.yml)
             │     ├─ Run block resolvers (PHP classes)
             │     └─ Inject shared data (site, nav, etc.)
             ├─→ Build Inertia props:
             │     ├─ content: processed markdown string
             │     ├─ sections: resolved block props
             │     ├─ header/footer: resolved block props
             │     ├─ navigation: built from vault hierarchy
             │     ├─ references: inline @ref data for popups
             │     └─ meta: SEO, breadcrumbs, prev/next
             │
             └─→ Inertia::render() ──────────────────→ Page component
                                                        ├─ Header block
                                                        ├─ Section blocks (registry)
                                                        ├─ MarkdownRenderer
                                                        │    ├─ remark/rehype pipeline
                                                        │    ├─ callouts, admonitions
                                                        │    ├─ code highlighting
                                                        │    ├─ inline @ref popups
                                                        │    └─ TOC extraction
                                                        └─ Footer block
```

## 8. Other considerations

- **Page data files** — `page-name.yml` sibling that serves as the page's "database", separating data from markdown presentation
- **Computed props** — reading time, word count, last modified (git/filesystem), author (git blame/frontmatter)
- **Conditional content** — show/hide blocks based on variables (`if: hasApiKey`)
- **Block slots** — zones where a block accepts free markdown, via `---` separator inside `@` blocks
- **Block wrappers** — common props for all blocks: background color, spacing, container width, anchor ID
- **Data cascade** — data inheritance: global Plume config → vault config → folder/index.yml → page frontmatter (each level can override the previous)
- **Header/Footer per vault** — a vault can override the global header/footer, or use the global one
- **Language file strategy** — subfolder (`content/docs/pt/intro.md`) vs suffix (`content/docs/intro.pt.md`) — configurable per vault
