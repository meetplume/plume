<?php

use App\Models\User;
use Filament\Facades\Filament;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $user = User::factory()->create();
    actingAs($user);
});

test('filament resources have no smoke', function () {

    $models   = [
        App\Models\Category::class,
        App\Models\Comment::class,
        App\Models\Page::class,
        App\Models\PanAnalytics::class,
        App\Models\Post::class,
        App\Models\Tag::class,
        App\Models\User::class,
    ];

    // Gives the list of models that have a Filament resource
    $resources = collect($models)->filter(fn($model) => Filament::getModelResource($model));

    $resourcesListUrls = $resources->map(function ($model) {
        try{return Filament::getResourceUrl($model, 'index');}
        catch(Exception){return null;}
    })->filter();

    $resourcesCreateUrls = $resources->map(function ($model) {
        try{return Filament::getResourceUrl($model, 'create');}
        catch(Exception){return null;}
    })->filter();

    $resourcesEditUrls = $resources->map(function ($model) {
        try{return Filament::getResourceUrl($model, 'edit', ['record' => 1]);}
        catch(Exception){return null;}
    })->filter();

    $resourcesViewUrls = $resources->map(function ($model) {
        try{return Filament::getResourceUrl($model, 'view', ['record' => 1]);}
        catch(Exception){return null;}
    })->filter();

    $resourcesUrls = [
        ...$resourcesListUrls,
        ...$resourcesCreateUrls,
        ...$resourcesEditUrls,
        ...$resourcesViewUrls,
    ];

    $this->assertCount(16, $resourcesUrls);

    $pages = visit($resourcesUrls);
    $pages->assertNoSmoke();
});

test('filament pages have no smoke', function () {

    $filamentPages = Filament::getPages();

    $resourcesUrls = collect($filamentPages)
        ->map(fn($page) => app($page)::getUrl())
        ->values()
        ->toArray();

    $pages = visit($resourcesUrls);
    $pages->assertNoSmoke();
});
