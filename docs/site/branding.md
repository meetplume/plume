---
title: Branding
description: Set the site name, logo, favicon and canonical URL.
---

# Branding

Site-wide branding is configured on the `Plume` facade and shared across every vault.

```php
use Meetplume\Plume\Facades\Plume;

Plume::configure()
    ->name('Acme Docs')
    ->url('https://docs.acme.com')
    ->logo('/images/logo.svg', '/images/logo-dark.svg')
    ->favicon('/favicon.ico')
    ->theme('default')
    ->vaults([ /* ... */ ]);
```

## Methods

| Method                 | Description                                                          |
|------------------------|----------------------------------------------------------------------|
| `name($name)`          | The site name. Defaults to `config('app.name')`.                     |
| `url($url)`            | The canonical site URL.                                              |
| `logo($light, $dark?)` | Logo image(s). The second argument is an optional dark-mode variant. |
| `favicon($path)`       | The favicon path.                                                    |
| `theme($preset)`       | A global [theme preset](/docs/appearance/presets).                   |

## Logo

`logo()` accepts a light-mode image and an optional dark-mode image. When a dark logo
is provided, Plume swaps to it automatically in dark mode:

```php
->logo('/images/logo.svg', '/images/logo-dark.svg')
```

Pass a single argument to use the same logo in both modes:

```php
->logo('/images/logo.svg')
```

The logo and name appear in the [header](/docs/site/header), linking back to the
vault's home.

::: note
`theme()` here sets a **global** preset for vaults that don't define their own
`config.yml`. A vault's own [`config.yml`](/docs/appearance/theming) always takes
precedence.
:::
