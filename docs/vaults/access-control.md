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

## Middleware

`canAccess()` answers yes or no: a guest gets a `403`, not your login page. To run
middleware before it — send guests to log in, keep the vault off some hosts —
override `middleware()`. It applies to the vault's pages, search index and content
assets. Keep `web`, which carries the session:

```php
public function middleware(): array
{
    return ['web', 'auth'];
}
```

::: note
Plume renders its own Inertia pages. If your `web` group carries your app's Inertia
middleware (`HandleInertiaRequests`), Plume leaves it out of the vault's routes: its
asset version would never match Plume's, and every visit would end in a full reload.
:::

## Per-page visibility

`canAccess()` opens or closes the whole vault. To show some pages to some readers only,
override `canSeePage()`. It receives the request, the page's key (its path without
extension) and its frontmatter — so a page can say what it documents:

```markdown
---
title: Opportunities pipeline
feature: crm
permission: opportunities.view
---
```

```php
public function canSeePage(Request $request, string $key, array $frontmatter): bool
{
    $feature = $frontmatter['feature'] ?? null;
    $permission = $frontmatter['permission'] ?? null;

    return ($feature === null || Feature::active($feature))
        && ($permission === null || $request->user()?->can($permission));
}
```

A page the reader does not see is left out of the sidebar, the search results, the
previous/next links and the tabs, and answers `404` — as if it did not exist.

## Page actions

A page's Markdown, served at its address followed by `.md`, goes through
`canAccess()` too. ChatGPT and Claude cannot read a vault behind a login, though:
see [Page Actions](/docs/vaults/page-actions) to leave them out of the page menu.
