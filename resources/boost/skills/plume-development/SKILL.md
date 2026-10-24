---
name: plume-development
description: Build and maintain documentation, pages and wikis from Markdown with meetplume/plume. Covers scaffolding and registering vaults, writing pages (frontmatter, callouts, links, images, code blocks), navigation, access control and per-page visibility, languages, theming, embedding a page in the app, and page actions. Activate when code extends `Meetplume\Plume\Vault` or calls `Plume::configure()`, when editing Markdown inside a vault's folder, or when asked to document a Laravel project with Plume.
license: MIT
metadata:
    author: meetplume
---

# Plume Development

Plume serves Markdown files from the project as pages of the Laravel app: same routes, middleware, session and authorization. A **vault** maps a folder of Markdown files to a URL prefix.

## Document a project, step by step

1. Scaffold a vault: `php artisan plume:vault Docs` creates `app/Plume/DocsVault.php` (served under `/docs` from `content/docs`, `docs` layout) and `content/docs/index.md`.
2. Register it in `AppServiceProvider::boot()`. An unregistered vault is the most common cause of a 404.

    ```php
    use App\Plume\DocsVault;
    use Meetplume\Plume\Facades\Plume;

    Plume::configure()->vaults([DocsVault::class]);
    ```

3. Prefer `Discovery::Auto` for docs that live next to the code: folders become sidebar groups, files become pages, no PHP per page.
4. Decide who reads it: `canAccess()` returns `false` by default (403). Override it, and add `auth` to `middleware()` to send guests to the login page.
5. Write the pages (below). Link them to each other with relative `.md` paths.
6. On deploy, run `php artisan plume:update-assets` after `composer install`, so the published front-end matches the installed version.

## The vault

```php
use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Vault;

class DocsVault extends Vault
{
    protected string $prefix = '/docs';

    protected string $path = 'docs';          // relative to base_path(), or absolute

    protected string $layout = 'docs';        // page, docs, wiki, api, blog, changelog

    protected Discovery $discovery = Discovery::Auto;

    public function canAccess(Request $request): bool
    {
        return $request->user() !== null;
    }

    public function middleware(): array
    {
        return ['web', 'auth'];               // keep `web`: it carries the session
    }
}
```

| Method                                    | Use it for                                                                                                                                           |
| ----------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- |
| `navigation()`, `pages()`                 | Manual sidebar (`NavGroup`, `Page`) and extra or absolute routes. With Auto discovery, the folders make the sidebar instead.                         |
| `tabs()`, `versions()`                    | Top-level tabs, versioned docs (`/docs/{version}/{tab}/{slug}`).                                                                                     |
| `languages()`                             | One folder per language code (`docs/en`, `docs/fr`).                                                                                                 |
| `languageFor(Request)`                    | The app picks the language (tenant setting, user preference): no switcher, no segment in URLs, untranslated pages fall back to the default language. |
| `canSeePage(Request, $key, $frontmatter)` | Per-page visibility. A hidden page leaves the sidebar, search and prev/next, and answers 404.                                                        |
| `backLink(Request)`                       | A "back to the app" link at the bottom of the sidebar.                                                                                               |
| `pageActions()`                           | The page menu. `PageAction::internal()` for a vault behind a login: ChatGPT and Claude cannot fetch it.                                              |

Per-page visibility reads what the page declares in its frontmatter:

```php
public function canSeePage(Request $request, string $key, array $frontmatter): bool
{
    $permission = $frontmatter['permission'] ?? null;

    return $permission === null || $request->user()?->can($permission) === true;
}
```

Appearance lives in the vault's `config.yml` (`primary`, `gray`, `radius`, `code_theme_light`, `code_theme_dark`, `customizer`). Site-wide branding goes through `Plume::configure()` (`name()`, `logo()`, `favicon()`, `appearanceKey()` to share the app's light/dark choice), or the `head` and `brand` partials published under `resources/views/vendor/plume/`.

## Writing a page

```md
---
title: Deploying
description: Ship a release to production and roll it back.
order: 2
group_label: Guides
---

# Deploying
```

| Key                         | Effect                                                                                                           |
| --------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| `title`                     | Page title and sidebar label. Without it, the first `#` heading.                                                 |
| `description`               | The page's meta description.                                                                                     |
| `order`                     | Position in its group, lowest first. A group sorts by its lowest page.                                           |
| `group_label`, `group_icon` | The group's label and Lucide icon (Auto discovery), read from the folder's first file: repeat them on each page. |
| `slug`, `hidden`            | Another URL segment; served but left out of the sidebar.                                                         |
| Any other key               | Free for `canSeePage()` (say `feature:` or `permission:`).                                                       |

- A folder's `index.md`, or its `README.md` when it has no `index.md`, is the folder's page. The root one is the vault's home.
- Files and folders starting with `_` are not served.

**Callouts**: `tip`, `info`, `note`, `warning`, `danger`, and the collapsible `details`. Give a title in brackets; GitHub's `> [!TIP]` syntax also works, without a custom title.

```md
:::warning[Run the migrations first]
The release reads the new columns on boot.
:::

::: details Full output
...
:::
```

**Links**: write them as on GitHub, with the relative path of the Markdown file. Plume points them at the page, and they keep working on GitHub.

```md
See [the setup](../getting-started/installation.md#requirements).
```

**Images and files**: keep them next to the page and reference them relatively. Plume serves them at `/{prefix}/_content/{path}`, behind the same access rules as the page. Absolute paths (`/images/…`) are left as they are. In a vault with languages, a page's relative paths resolve inside its language folder.

```md
![The deploy button in the release panel](images/deploy-button.webp)
```

**Code blocks** are rendered by Expressive Code: ` ```php title="routes/web.php" {3-5} `, and `wrap` to wrap long lines. Raw HTML, inline SVG included, is rendered.

## Embedding a page in the app

Load the page with `?embed` in an iframe of the same origin: the content alone, without header or sidebars, in the compact type scale (`&scale=base` or `lg` for more). An anchor scrolls to its section. On each page it shows, the frame posts `{ type: 'plume:page', title, url }` to the parent window, so a drawer can follow the reader's navigation and offer "open full page".

```html
<iframe src="/docs/guides/deploy?embed#rollback" title="Help"></iframe>
```

## Pitfalls

- A vault that is not in `Plume::configure()->vaults([...])` is not routed.
- `canAccess()` denies by default; a guest gets a 403 unless `middleware()` adds `auth`.
- Plume renders its own Inertia pages and leaves the app's `HandleInertiaRequests` out of its routes: do not add it back to a vault's middleware.
- A link written as an absolute URL (`/docs/guides/deploy`) works in Plume but breaks on GitHub: prefer relative `.md` paths.
- After updating the package, `php artisan plume:update-assets`, or the browser keeps the old front-end.
