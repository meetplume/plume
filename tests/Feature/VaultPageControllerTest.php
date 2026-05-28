<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\ForbiddenTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([ForbiddenTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();
});

it('returns 403 when the vault denies access', function (): void {
    $this->get('/forbidden')->assertForbidden();
});

it('returns 403 for nested pages when the vault denies access', function (): void {
    $this->get('/forbidden/guides/deploy')->assertForbidden();
});
