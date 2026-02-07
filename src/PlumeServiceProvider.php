<?php

declare(strict_types=1);

namespace Meetplume\Plume;

class PlumeServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'plume');

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/plume/dist'),
        ], 'plume-assets');
    }
}
