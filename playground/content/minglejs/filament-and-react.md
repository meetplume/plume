# Filament & React

For Filament, we have to register a hook, so it enables the `viteReactRefresh` plugin.

This is only needed while `npm run dev` is running. After a `npm run build`, the hook is not needed anymore.
So, consider applying only when in local development.

Check below the example(s) on the Filament DemoReact page.

```php {6-8,18-23}
<?php

namespace App\Filament\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Foundation\Vite;

class DemoReact extends Page
{
    protected static ?string $navigationIcon = 'icon-react';

    protected static string $view = 'filament.filament.pages.demo-react';

    public function mount()
    {
        if (app()->environment('local')) {
            FilamentView::registerRenderHook(
                name: PanelsRenderHook::HEAD_START,
                hook: fn() => app(Vite::class)->reactRefresh(),
            );
        }
    }
}
```
