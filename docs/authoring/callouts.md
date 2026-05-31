---
title: Callouts
description: "Tip, info, note, warning and danger callouts using the ::: syntax."
---

# Callouts

Plume supports callout directives using the `:::` fenced syntax, similar to VitePress
and Docusaurus. It also supports the GitHub `> [!TIP]` syntax.

## Basic callouts

```md
::: tip
Callouts can include `code` and other [markdown](https://en.wikipedia.org/wiki/Markdown) elements.
:::
```

::: tip
Callouts can include `code` and other [markdown](https://en.wikipedia.org/wiki/Markdown) elements.
:::

The five built-in types are `tip`, `info`, `note`, `warning` and `danger`:

```md
::: info
Useful for supplementary information that doesn't fit in the main text.
:::

::: note
Great for additional context or references.
:::

::: warning
Make sure to run `npm run build` after changing the configuration.
:::

::: danger
Never commit your `.env` file. It contains sensitive credentials.
:::
```

::: info
Useful for supplementary information that doesn't fit in the main text.
:::

::: warning
Make sure to run `npm run build` after changing the configuration.
:::

::: danger
Never commit your `.env` file. It contains sensitive credentials.
:::

## Custom titles

Provide a custom title in square brackets:

```md
:::tip[Pro Tip]
Use `php artisan route:list` to see all registered routes.
:::
```

:::tip[Pro Tip]
Use `php artisan route:list` to see all registered routes.
:::

## Alternative syntaxes

GitHub-style:

```md
> [!TIP]
> Use `php artisan route:list` to see all registered routes.
```

VitePress-style (title after the type):

```md
::: tip Pro Tip
Use `php artisan route:list` to see all registered routes.
:::
```

:::warning
Custom titles are not supported in the GitHub-style syntax.
:::

## Aliases

Some types alias to others for cross-platform compatibility:

- `important` renders as `info`
- `caution` renders as `danger`

```md
::: important
Renders as an info callout.
:::

::: caution
Renders as a danger callout.
:::
```

## Collapsible details

The `details` callout renders as a collapsible `<details>` element:

````md
::: details Click to expand
Hidden by default, toggled open by clicking the summary.

You can include any markdown — lists, **bold**, `code`, and code blocks:

```php
Route::get('/example', fn () => view('example'));
```
:::
````

::: details Click to expand
Hidden by default, toggled open by clicking the summary.

You can include any markdown — lists, **bold**, `code`, and code blocks:

```php
Route::get('/example', fn () => view('example'));
```
:::
