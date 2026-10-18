---
title: Theming
description: Tune colors, radius, spacing, dark mode and code themes with config.yml.
---

# Theming

Each vault can carry a `config.yml` in its content folder to control its appearance.
Plume looks for it at `{vault path}/config.yml` (e.g. `content/docs/config.yml`).

```yaml
# content/docs/config.yml
theme: default
primary: violet
gray: neutral
radius: large
spacing: default
dark: false
code_theme_light: material-theme-darker
code_theme_dark: material-theme-darker
```

## Options

| Key                | Values                                                                 | Default        |
|--------------------|------------------------------------------------------------------------|----------------|
| `theme`            | A [preset](/docs/appearance/presets): `default`, `brutalist`, `catppuccin`, `forest`, `ocean`, `rose` | — |
| `primary`          | Any Tailwind color (`blue`, `red`, `violet`, …) or an arbitrary value  | `neutral`      |
| `gray`             | `slate`, `gray`, `zinc`, `neutral`, `stone`, `mauve`, `olive`, `mist`, `taupe` | `neutral` |
| `radius`           | `none`, `small`, `medium`, `large`                                     | `medium`       |
| `spacing`          | `dense`, `compact`, `default`, `spacious`                              | `default`      |
| `dark`             | `true`, `false`                                                        | `false`        |
| `code_theme_light` | An [Expressive Code](https://expressive-code.com/guides/themes/) theme | `github-light` |
| `code_theme_dark`  | An [Expressive Code](https://expressive-code.com/guides/themes/) theme | `github-dark`  |
| `customizer`       | `true`, `false` — toggles the local live customizer                    | `true`         |

## How values resolve

Each setting is resolved in this order:

1. The value in the vault's `config.yml`.
2. The value from the chosen `theme` preset.
3. Plume's built-in default.

This means you can pick a preset and selectively override individual values — for
example, keep the `default` preset but switch the primary color:

```yaml
theme: default
primary: emerald
```

Invalid values fall back to the default, so a typo in `radius` or `code_theme_*` won't
break rendering.

## Live customizer

In your local environment, Plume ships a live customizer that lets you tweak the theme
in the browser and write the result back to `config.yml`. It's enabled by default
locally and can be turned off:

```yaml
customizer: false
```

The customizer only runs in the `local` environment — it never appears in production.

## Sharing light and dark with your app

Plume keeps the reader's light/dark choice in `localStorage` (and a cookie) under the
`theme` key. Inside an app that has its own switch, give Plume the app's key so both
read and write the same choice — a stored `system` follows the operating system:

```php
Plume::configure()
    ->appearanceKey('appearance');
```

## Adding to the page head

To add styles, a favicon or a script to every Plume page, publish your own
`resources/views/vendor/plume/head.blade.php`. It is rendered after Plume's stylesheet,
so its CSS variables win — for instance to map your app's colors onto Plume's:

```blade
<style>
    html:root {
        --primary: var(--brand-primary);
        --background: var(--brand-background);
    }
</style>
```

::: note
A Plume theme (`->theme()` or a vault's `config.yml`) sets its colors inline on `<html>`,
which beats any stylesheet. Leave the theme unset when the host provides the colors, or
mark your variables `!important`.
:::

## Fonts

Plume sets its text in Tailwind's `--font-sans`. To use your app's font, load it and
redefine the variable from the head partial:

```blade
<link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,500,600,700">
<style>
    html:root {
        --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    }
</style>
```

A font your app bundles with Vite can be loaded with an `@font-face` whose `src` is
`Vite::asset('path/to/font.woff2')`.
