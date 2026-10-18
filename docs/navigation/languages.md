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

Now the Portuguese installation page is reachable at `/docs/pt/instalacao`.

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
