---
title: Footer
description: Configure the site footer with link columns and a tagline.
---

# Footer

The footer is configured globally on the `Plume` facade. Build one with
`Footer::make()` and pass it to `->footer()`:

```php
use Meetplume\Plume\Facades\Plume;
use Meetplume\Plume\Footer;
use Meetplume\Plume\FooterColumn;

Plume::configure()
    ->footer(
        Footer::make('footer1')
            ->text('Built with Plume')
            ->columns([
                FooterColumn::make('Product')->links([
                    ['label' => 'Docs', 'href' => '/docs'],
                    ['label' => 'Changelog', 'href' => '/changelog'],
                ]),
                FooterColumn::make('Community')->links([
                    ['label' => 'GitHub', 'href' => 'https://github.com/meetplume/plume'],
                ]),
            ])
    )
    ->vaults([ /* ... */ ]);
```

## Footer builder

| Method            | Description                                                  |
|-------------------|--------------------------------------------------------------|
| `make($component)`| The footer component to render, e.g. `footer1` or `footer2`. |
| `text($text)`     | A tagline or copyright line.                                 |
| `columns([...])`  | Columns of links — an array of `FooterColumn` objects.       |

## Footer columns

Each `FooterColumn` has a label and a list of links:

```php
FooterColumn::make('Resources')->links([
    ['label' => 'Documentation', 'href' => '/docs'],
    ['label' => 'API Reference', 'href' => '/api'],
]);
```

A minimal footer can be just a line of text:

```php
Footer::make('footer1')->text('Built with Plume');
```

## Plume credit

The docs footer ends with *Powered by Plume*. To leave it out:

```php
Plume::configure()
    ->poweredBy(false);
```

