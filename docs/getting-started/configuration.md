# Configuration

A **vault** maps a folder of Markdown files to a URL prefix in your application. Create one by
extending `Meetplume\Plume\Vault`:

```php
namespace App\Plume;

use Meetplume\Plume\Vault;

class HandbookVault extends Vault
{
    protected string $prefix = '/handbook';

    protected string $path = 'content/handbook';

    protected string $layout = 'docs';
}
```

- `prefix` — the URL prefix the vault is served under.
- `path` — the folder (relative to your project root) holding the Markdown files.
- `layout` — the Blade layout used to render pages.

Register the vault with the `Plume` facade, typically in a service provider's `boot()` method.
See [Usage](/docs/usage/vaults) for the full picture.
