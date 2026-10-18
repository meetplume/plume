---
title: Access Control
description: Gate a vault behind authentication or any custom logic with canAccess().
---

# Access Control

Every vault has a `canAccess()` method that decides whether the current request may
view its pages. It receives the `Illuminate\Http\Request` and returns a boolean.

```php
use Illuminate\Http\Request;

public function canAccess(Request $request): bool
{
    return true;
}
```

::: warning
`canAccess()` defaults to **`false`**. A freshly extended vault denies all access until
you override it. The `plume:vault` scaffold returns `true` for you so new vaults work
out of the box.
:::

When `canAccess()` returns `false`, Plume aborts the request with a `403`.

## Public vault

```php
public function canAccess(Request $request): bool
{
    return true;
}
```

## Authenticated users only

```php
public function canAccess(Request $request): bool
{
    return $request->user() !== null;
}
```

## Role or permission checks

Because you get the full request, any authorization logic works — Gates, policies,
roles, feature flags, IP allow-lists, and so on:

```php
public function canAccess(Request $request): bool
{
    return $request->user()?->can('view-internal-docs') ?? false;
}
```

```php
public function canAccess(Request $request): bool
{
    return $request->user()?->hasRole('staff') ?? false;
}
```

::: tip
Access is evaluated per request, so you can vary it by environment, header, or any
runtime condition — for example, opening a vault publicly in production but requiring
auth in staging.
:::

## Page actions

A page's Markdown, served at its address followed by `.md`, goes through
`canAccess()` too. ChatGPT and Claude cannot read a vault behind a login, though:
see [Page Actions](/docs/vaults/page-actions) to leave them out of the page menu.
