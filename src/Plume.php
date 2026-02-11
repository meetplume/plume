<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Http\Controllers\CollectionController;

class Plume
{
    public function __construct(private CollectionRegistry $registry) {}

    public function collection(string $prefix, string $contentPath): Collection
    {
        $collection = new Collection($prefix, $contentPath);

        $this->registry->register($collection);

        $trimmedPrefix = trim($prefix, '/');

        Route::get($prefix, [CollectionController::class, 'index'])
            ->defaults('prefix', $prefix)
            ->name("plume.{$trimmedPrefix}.index");

        Route::get("{$prefix}/{slug}", [CollectionController::class, 'show'])
            ->where('slug', '[a-zA-Z0-9\-\/]+')
            ->defaults('prefix', $prefix)
            ->name("plume.{$trimmedPrefix}.show");

        return $collection;
    }
}
