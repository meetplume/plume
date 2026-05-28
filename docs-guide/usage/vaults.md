# Vaults

Register your vaults with the `Plume` facade so they are served by your application:

```php
use App\Plume\HandbookVault;
use Meetplume\Plume\Facades\Plume;

Plume::vaults([
    HandbookVault::class,
]);
```

Each vault is independent: it has its own URL prefix, content folder, layout and navigation. Add as
many vaults as you need — one for a marketing page, another for product docs, another for a wiki.
