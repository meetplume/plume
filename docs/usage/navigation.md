# Navigation

Define a sidebar for a vault by overriding the `navigation()` method. Group related pages with
`NavGroup` and declare each page with `Page`:

```php
use Meetplume\Plume\NavGroup;
use Meetplume\Plume\Page;

public function navigation(): array
{
    return [
        NavGroup::make('getting-started')
            ->icon('rocket')
            ->pages([
                Page::make('index')->label('Home')->home(),
                Page::make('installation')->label('Installation'),
            ]),
    ];
}
```

Each `Page::make()` argument is the Markdown file path (without extension) relative to the vault's
`path`. Use `->label()` to set the link text and `->home()` to mark the landing page.
