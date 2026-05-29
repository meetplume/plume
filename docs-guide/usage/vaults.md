# Vaults

Register your vaults with the `Plume` facade so they are served by your application:

```php
<?php

namespace App\Providers;

use App\Plume\DocsGuideVault;
use Illuminate\Support\ServiceProvider;
use Meetplume\Plume\Facades\Plume;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Plume::configure()
            ->vaults([
                DocsGuideVault::class,
            ]);
    }
}

```

Each vault is independent: it has its own URL prefix, content folder, layout and navigation. Add as
many vaults as you need — one for a marketing page, another for product docs, another for a wiki.
