<?php

namespace App\Providers;

use App\Enums\SiteSettings;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Skip during console commands to avoid issues during migrations
        if ($this->app->runningInConsole()) {
            return;
        }

        // Register a callback to be executed after all service providers have been registered
        $this->app->booted(function () {
            try {
                // Override mail configuration with settings
                config([
                    'mail.default' => SiteSettings::MAIL_MAILER->get(),
                    'mail.mailers.smtp.host' => SiteSettings::MAIL_HOST->get(),
                    'mail.mailers.smtp.port' => SiteSettings::MAIL_PORT->get(),
                    'mail.mailers.smtp.username' => SiteSettings::MAIL_USERNAME->get(),
                    'mail.mailers.smtp.password' => SiteSettings::MAIL_PASSWORD->get(),
                    'mail.mailers.smtp.encryption' => SiteSettings::MAIL_ENCRYPTION->get(),
                    'mail.from.address' => SiteSettings::MAIL_FROM_ADDRESS->get(),
                    'mail.from.name' => SiteSettings::MAIL_FROM_NAME->get(),
                ]);
            } catch (\Exception $e) {
                // Log the error but don't crash the application
                logger()->error('Failed to load mail settings: ' . $e->getMessage());
            }
        });
    }
}
