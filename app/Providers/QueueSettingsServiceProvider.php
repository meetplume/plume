<?php

namespace App\Providers;

use App\Enums\SiteSettings;
use Illuminate\Support\ServiceProvider;

class QueueSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only set the queue connection if facades are available (not during console commands like config:cache)
        if (!app()->runningInConsole() || app()->runningUnitTests()) {
            try {
                $queueConnection = SiteSettings::QUEUE_CONNECTION->get();
                config(['queue.default' => $queueConnection]);
            } catch (\Exception $e) {
                config(['queue.default' => 'sync']);
            }
        }
    }
}
