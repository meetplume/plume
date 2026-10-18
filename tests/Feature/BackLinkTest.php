<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\BackLinkTestVault;
use Tests\Fixtures\SearchTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([SearchTestVault::class, BackLinkTestVault::class])->boot();
});

it('links back to the app only when the vault names the way back', function (string $url, ?array $backLink): void {
    $this->get($url, ['X-Inertia' => 'true'])
        ->assertOk()
        ->assertJsonPath('props.backLink', $backLink);
})->with([
    'without a way back' => ['/docs', null],
    'with a way back' => ['/help', ['label' => 'Back to the app', 'href' => '/dashboard']],
]);
