<?php

declare(strict_types=1);

use Meetplume\Plume\Plume;
use Tests\Fixtures\VisibilityTestVault;

beforeEach(function (): void {
    app()->forgetInstance(Plume::class);
    app(Plume::class)->configure()->vaults([VisibilityTestVault::class]);
    app(Plume::class)->getConfiguration()->boot();
});

it('answers 404 for a page the reader does not see', function (string $query, int $status): void {
    $this->get('/visibility/guides/payroll'.$query, ['X-Inertia' => 'true'])->assertStatus($status);
})->with([
    'hidden from this reader' => ['', 404],
    'shown to this reader' => ['?staff=1', 200],
]);

it('leaves a hidden page out of the sidebar and the previous/next links', function (): void {
    $response = $this->get('/visibility/guides/start', ['X-Inertia' => 'true'])->assertOk();

    $labels = collect($response->json('props.navigation'))->flatMap(fn (array $group): array => array_column($group['pages'], 'label'));

    expect($labels->all())->not->toContain('Payroll')
        ->and($response->json('props.next.label'))->toBe('Support');
});

it('leaves a hidden page out of the search index, which keeps its filter fields to itself', function (): void {
    $hidden = $this->get('/visibility/_plume/search-index.json')->assertOk();
    $shown = $this->get('/visibility/_plume/search-index.json?staff=1')->assertOk();

    expect(array_column($hidden->json(), 'slug'))->not->toContain('guides/payroll')
        ->and(array_column($shown->json(), 'slug'))->toContain('guides/payroll')
        ->and($shown->json('0'))->not->toHaveKeys(['key', 'frontmatter'])
        ->and($hidden->headers->get('ETag'))->not->toBe($shown->headers->get('ETag'));
});
