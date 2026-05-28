---
title: Callouts
description: "Callout boxes using the ::: syntax for tips, warnings, notes, and more."
---

# Callouts

Plume supports callout directives using the `:::` fenced syntax, similar to VitePress and Docusaurus.
It also supports GitHub syntax like `> [!TIP]`.

## Basic Callouts

```md
::: tip
Callouts can include `code` blocks and other [markdown](https://en.wikipedia.org/wiki/Markdown) elements
:::
```

::: tip
Callouts can include `code` blocks and other [markdown](https://en.wikipedia.org/wiki/Markdown) elements
:::

```md
::: info
This callout is useful for supplementary information that doesn't fit in the main text.
:::
```

::: info
This callout is useful for supplementary information that doesn't fit in the main text.
:::

```md
::: note
Notes are great for additional context or references that complement the main content.
:::
```

::: note
Notes are great for additional context or references that complement the main content.
:::

```md
::: warning
Make sure to run `npm run build` after making changes to the configuration.
:::
```

::: warning
Make sure to run `npm run build` after making changes to the configuration.
:::

```md
::: danger
Never commit your `.env` file to version control. It contains sensitive credentials.
:::
```

::: danger
Never commit your `.env` file to version control. It contains sensitive credentials.
:::

## Custom Titles

You can provide a custom title for any callout:

```md
:::tip[Pro Tip]
Use `php artisan route:list` to see all registered routes in your application.
:::
```

:::tip[Pro Tip]
Use `php artisan route:list` to see all registered routes in your application.
:::

## Alternatives Syntaxes

You can also use the GitHub-style syntax:

```md
> [!TIP]
> Use `php artisan route:list` to see all registered routes in your application.
```

:::warning
Custom titles are not supported in GitHub-style syntax.
:::

Or the VitePress-style syntax:

```md
::: tip Pro Tip
Use `php artisan route:list` to see all registered routes in your application.
:::
```

## Aliases

Some callout types have aliases for compatibility with other platforms:

- `important` renders as `info`
- `caution` renders as `danger`

```md
::: important
This will render as an info callout.
:::
```

::: important
This will render as an info callout.
:::

```md
::: caution
This will render as a danger callout.
:::
```

::: caution
This will render as a danger callout.
:::

## Collapsible Details

The `details` callout renders as a collapsible element:

````md
::: details Click to expand
This content is hidden by default and can be toggled open/closed by clicking the summary.

You can include any markdown content here, including:

- Lists
- **Bold text**
- `Inline code`


Or code blocks of course:

```php
Route::get('/example', function () {
    return view('example');
});
```
:::
````

::: details Click to expand
This content is hidden by default and can be toggled open/closed by clicking the summary.

You can include any markdown content here:

- Lists
- **Bold text**
- `Inline code`

Or code blocks of course:

```php
Route::get('/example', function () {
    return view('example');
});
```
:::
