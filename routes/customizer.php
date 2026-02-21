<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Meetplume\Plume\Http\Controllers\CustomizerController;

Route::post('/_plume/customizer', [CustomizerController::class, 'update']);
Route::post('/_plume/customizer/reset', [CustomizerController::class, 'reset']);
