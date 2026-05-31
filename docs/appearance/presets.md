---
title: Theme Presets
description: Ready-made themes you can apply with a single config value.
---

# Theme Presets

A preset is a bundle of theme defaults (primary color, gray scale, radius, spacing,
dark mode, code themes). Apply one with the `theme` key in your vault's
[`config.yml`](/docs/appearance/theming):

```yaml
theme: ocean
```

## Available presets

| Preset       | Character                                              |
|--------------|--------------------------------------------------------|
| `default`    | Clean, neutral baseline. A good starting point.        |
| `brutalist`  | High-contrast, sharp edges, bold.                      |
| `catppuccin` | Soft, pastel palette inspired by the Catppuccin theme. |
| `forest`     | Earthy greens.                                         |
| `ocean`      | Cool blues.                                            |
| `rose`       | Warm pinks and reds.                                   |

## Overriding a preset

A preset sets defaults; any key you also specify in `config.yml` wins. Start from a
preset and adjust just what you need:

```yaml
theme: forest
primary: lime      # override the preset's primary color
radius: none       # square off the corners
```

See [Theming](/docs/appearance/theming) for the full list of overridable keys and how
values resolve.
