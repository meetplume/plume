---
title: Versions
description: Serve multiple versions of your documentation from one vault.
---

# Versions

Versions let one vault serve several editions of the same documentation — `v1`, `v2`,
a `next` preview, and so on. Override the `versions()` method:

```php
use Meetplume\Plume\Version;

public function versions(): array
{
    return [
        Version::make('v2')->default(),
        Version::make('v1'),
    ];
}
```

## Version builder

| Method        | Description                                                         |
|---------------|---------------------------------------------------------------------|
| `make($key)`  | Creates the version. The key appears in the URL.                    |
| `default()`   | Marks this version as the default (served at the bare prefix).      |
| `tabs()`      | Optional [tabs](/docs/navigation/tabs) specific to this version.    |

If no version is marked `default()`, the first one is used.

## Content layout on disk

Each version reads from a subfolder named after its key, inside the vault's `path`:

```
content/docs/
├── v2/
│   ├── installation.md
│   └── configuration.md
└── v1/
    ├── installation.md
    └── configuration.md
```

## URLs

The version key becomes a URL segment:

```
/docs/{version}/{slug}
/docs/v2/installation
/docs/v1/installation
```

Requests without a version segment resolve to the default version, so `/docs/installation`
serves the default version's page.

## Per-version tabs

A version can define its own tabs, letting structure evolve between releases:

```php
public function versions(): array
{
    return [
        Version::make('v2')->default()->tabs([
            Tab::make('guides')->groups([ /* ... */ ]),
            Tab::make('api')->groups([ /* ... */ ]),
        ]),
        Version::make('v1')->tabs([
            Tab::make('guides')->groups([ /* ... */ ]),
        ]),
    ];
}
```

When both versions and tabs are present, the URL nests as
`/docs/{version}/{tab}/{slug}`.
