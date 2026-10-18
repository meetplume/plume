---
title: Page Actions
description: Copy a page as Markdown, open it in ChatGPT or Claude, or save it as a PDF.
---

# Page Actions

Next to the title of every page, a **Copy page** button copies the page's Markdown,
ready to paste into an AI assistant. Its menu offers more:

| Action           | What it does                                                               |
|------------------|----------------------------------------------------------------------------|
| Copy page        | Copies the page's Markdown, without its frontmatter.                       |
| View as Markdown | Opens the page's Markdown in a new tab.                                    |
| Open in ChatGPT  | Opens ChatGPT, asked to read the page and answer questions about it.       |
| Open in Claude   | Opens Claude, asked the same.                                              |
| Export as PDF    | Opens the browser's print dialog, to print the page or save it as a PDF.   |

The menu shows on the `docs`, `wiki`, `api` and `page` layouts, beside the page's
`#` title, or above the page when it opens without one. Landing pages, whose title
is in their [sections](/docs/authoring/landing-pages), go without it.

Its words follow the language of the page, like the rest of Plume's
[interface](/docs/navigation/languages#interface-texts), and so does the question
put to ChatGPT and Claude.

## The Markdown of a page

Every page is also served as Markdown, at its address followed by `.md`:

| Page                                  | Markdown                                 |
|---------------------------------------|------------------------------------------|
| `/docs/getting-started/installation`  | `/docs/getting-started/installation.md`  |
| `/docs` (the vault home)              | `/docs.md`                               |

The response is the page's Markdown file without its frontmatter, as
`text/markdown`. It goes through the same middleware and `canAccess()` as the page:
a reader who cannot open a page cannot read its Markdown either.

ChatGPT and Claude are given this address rather than the page's own: Plume renders
pages in the browser, so the page's HTML holds no text they could read.

## Choosing the actions

Override `pageActions()` in your vault to pick the entries of the menu:

```php
use Meetplume\Plume\Enums\PageAction;

public function pageActions(): array
{
    return [
        PageAction::Copy,
        PageAction::ViewMarkdown,
        PageAction::ExportPdf,
    ];
}
```

Return `[]` to leave the menu out. The `.md` addresses stay: they serve nothing a
reader of the page cannot see already.

## Vaults behind a login

ChatGPT and Claude fetch the page from their own servers, without your readers'
session: they cannot read a vault behind a login. `PageAction::internal()` keeps the
actions that stay on your site:

```php
use Meetplume\Plume\Enums\PageAction;

public function pageActions(): array
{
    return PageAction::internal();
}
```
