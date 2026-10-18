---
title: Embedding
description: Show a page of a vault inside your app, in a drawer or a dialog.
---

# Embedding

An app can show a page of its documentation without leaving the current screen:
a help button opens the page in a drawer or a dialog. Add `?embed` to the page's
address and load it in an iframe:

```html
<iframe src="/docs/guides/deploy?embed#rollback" title="Help"></iframe>
```

The page then shows its content alone: no header, no sidebar, no table of
contents. An anchor scrolls to its section.

## Text size

A panel sits beside an app whose own text is usually smaller than a
documentation page's, so an embedded page uses the compact type scale. Pick
another one with `scale`:

| `scale` | Paragraphs | Use it for                           |
|---------|------------|--------------------------------------|
| `sm`    | 14 px      | A drawer or a dialog (the default).  |
| `base`  | 16 px      | A wide frame, as on the full pages.  |
| `lg`    | 18 px      | A frame meant for reading at length. |

```html
<iframe src="/docs/guides/deploy?embed&scale=base" title="Help"></iframe>
```

Code blocks and callouts keep their own size.

## Inside the frame

- The reader moves from page to page inside the frame. These visits replace the
  current entry of the browser's history instead of adding new ones, so the back
  button keeps working for the app once the frame is closed.
- A link to any other page of the site, such as a screen of the app, opens in
  the whole window. Links to other sites open in a new tab, as everywhere.
- The frame follows the light or dark choice of the full pages: it is stored for
  the site, not for the frame.

The vault's middleware and `canAccess()` apply as usual: an iframe from the same
domain sends the reader's session.

## Following the reader

On each page it shows, the frame posts a message to the app around it:

```js
window.addEventListener('message', (event) => {
    if (event.origin !== window.location.origin || event.data?.type !== 'plume:page') {
        return;
    }

    // { type: 'plume:page', title: 'Deploy', url: '/docs/guides/deploy#rollback' }
    drawerTitle.textContent = event.data.title;
    fullPageLink.href = event.data.url;
});
```

`title` is the page's title, `url` the address of the page as a full page,
without `?embed`. The message goes to the origin of the page that loaded the frame.
