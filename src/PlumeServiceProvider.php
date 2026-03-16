<?php

declare(strict_types=1);

namespace Meetplume\Plume;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Meetplume\Plume\Inertia\PlumeInertia;

class PlumeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Plume::class);
        $this->app->singleton(PlumeInertia::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'plume');

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/plume/dist'),
        ], 'plume-assets');

        $this->registerBladeDirectives();

        if ($this->app->environment('local', 'testing')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/customizer.php');
        }

        $this->app->booted(function (): void {
            $plume = app(Plume::class);
            $config = $plume->getConfiguration();

            if ($config !== null) {
                $config->boot();
            }
        });
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('plumeInertia', fn (): string => '<div id="app" data-page="<?php echo e(json_encode($page)); ?>"></div>');

        Blade::directive('plumeInertiaHead', fn (): string => '');
    }
}
