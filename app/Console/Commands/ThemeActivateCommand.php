<?php

namespace App\Console\Commands;

use App\Services\ThemeService;
use Illuminate\Console\Command;

class ThemeActivateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:activate {theme : The theme name to activate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a theme';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $themeName = $this->argument('theme');
        $themeService = app(ThemeService::class);

        // Check if theme exists
        if (!$themeService->themeExists($themeName)) {
            $this->error("Theme '{$themeName}' does not exist.");

            // Show available themes
            $availableThemes = array_keys($themeService->getAvailableThemes());
            if (!empty($availableThemes)) {
                $this->line('Available themes:');
                foreach ($availableThemes as $theme) {
                    $this->line("  - {$theme}");
                }
            }

            return 1;
        }


        $currentTheme = $themeService->getActiveTheme();

        if ($currentTheme === $themeName) {
            $this->info("Theme '{$themeName}' is already active.");
            return 0;
        }

        $this->info("Activating theme '{$themeName}'...");

        try {
            $success = $themeService->activateTheme($themeName);

            if (!$success) {
                $this->error('Failed to activate theme.');
                return 1;
            }

            $this->info("Theme '{$themeName}' has been activated successfully!");
            $this->line("Previous theme was: {$currentTheme}");
            $this->info('Theme assets published successfully.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Error activating theme: ' . $e->getMessage());
            return 1;
        }
    }
}
