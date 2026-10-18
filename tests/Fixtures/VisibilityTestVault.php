<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Http\Request;
use Meetplume\Plume\Enums\Discovery;
use Meetplume\Plume\Vault;

/** Pages meant for staff (`audience: staff`) show to a request that says `?staff=1`. */
class VisibilityTestVault extends Vault
{
    protected string $prefix = '/visibility';

    protected string $path = __DIR__.'/../Unit/fixtures/visibility';

    protected string $layout = 'docs';

    protected Discovery $discovery = Discovery::Auto;

    /**
     * @param  array<string, mixed>  $frontmatter
     */
    public function canSeePage(Request $request, string $key, array $frontmatter): bool
    {
        return ($frontmatter['audience'] ?? null) !== 'staff' || $request->boolean('staff');
    }

    public function canAccess(Request $request): bool
    {
        return true;
    }
}
