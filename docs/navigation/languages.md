---
title: Languages
description: Serve localized documentation with per-language folders and slugs.
---

# Languages

Languages let one vault serve localized content. Override the `languages()` method:

```php
use Meetplume\Plume\Language;

public function languages(): array
{
    return [
        Language::make('en', 'English')->default(),
        Language::make('pt', 'Português'),
    ];
}
```

## Language builder

| Method            | Description                                                          |
|-------------------|----------------------------------------------------------------------|
| `make($code, $name)` | Creates the language. `$code` is the URL segment, `$name` the label. |
| `default()`       | Marks this language as the default (served at the bare prefix).      |
| `slugs([...])`    | Per-page translated slugs (`key => translated-slug`).                |

If no language is marked `default()`, the first one is used.

## Content layout on disk

Each language reads from a subfolder named after its code:

```
content/docs/
├── en/
│   ├── installation.md
│   └── configuration.md
└── pt/
    ├── installation.md
    └── configuration.md
```

## URLs

The language code becomes a URL segment:

```
/docs/{language}/{slug}
/docs/en/installation
/docs/pt/installation
```

Requests without a language segment resolve to the default language.

## Translated slugs

To localize the slugs themselves (not just the content), map page keys to translated
slugs per language:

```php
public function languages(): array
{
    return [
        Language::make('en', 'English')->default(),
        Language::make('pt', 'Português')->slugs([
            'installation' => 'instalacao',
            'configuration' => 'configuracao',
        ]),
    ];
}
```

Now the Portuguese installation page is reachable at `/docs/pt/instalacao`, in the
sidebar, the search results and the language switcher alike. The key is the file path
without its extension (`guides/deploy` for `pt/guides/deploy.md`). With
[Auto discovery](/docs/vaults/discovery), a `slug` in the page's frontmatter does the same.

## Language switcher

When a vault has more than one language, the header shows a language menu, and the
mobile menu the same menu next to its light/dark switch. Each language links to **the
same page** in that language — the file at the same path in its folder — or to that
language's home while the page is not translated yet. Switching is an Inertia visit, so
the page does not reload.

::: note
Languages, versions and tabs can be combined. The URL segments nest in this order:
`/{prefix}/{language}/{version}/{tab}/{slug}` — any segment whose feature isn't used is
simply omitted.
:::

## Language picked by the app

When the language comes from the app rather than from the reader — a per-tenant
setting, a user preference — override `languageFor()`:

```php
public function languageFor(Request $request): ?string
{
    return app()->getLocale();
}
```

The vault then serves that language at the bare prefix (`/docs/installation`): there is
no language switcher and no language segment in routes or links. A page not translated
yet is served from the default language, and listed in the sidebar and search in its
place. A code the vault does not offer serves the default language.

## Interface texts

Plume's own words — search, *On this page*, menus, the copy button of code blocks —
follow the language of the page (or the app locale when the vault has no languages).
Plume ships French, Portuguese and Spanish; any other locale falls back to English.

They are Laravel JSON translations keyed by the English text. To change one, or to
add a language, put the key in your app's `lang/{locale}.json`:

```json
{
    "On this page": "In this guide"
}
```

The page's `<html lang>` follows the same locale.
