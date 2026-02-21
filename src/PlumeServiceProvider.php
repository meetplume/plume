<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\ServiceProvider;

class PlumeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Plume::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'plume');

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/plume/dist'),
        ], 'plume-assets');

        if ($this->app->environment('local', 'testing')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/customizer.php');
        }
    }
}
