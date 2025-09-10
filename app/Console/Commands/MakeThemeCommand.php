<?php

namespace App\Console\Commands;

use App\Services\ThemeService;
use Illuminate\Console\Command;

class MakeThemeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:theme {name : The name of the theme} {--copy-from= : Copy from an existing theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new theme';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $themeName = $this->argument('name');
        $copyFrom = $this->option('copy-from');

        $themeService = app(ThemeService::class);

        // Validate theme name
        if (!preg_match('/^[a-z][a-z0-9-]*$/', $themeName)) {
            $this->error('Theme name must start with a letter and contain only lowercase letters, numbers, and hyphens.');
            return 1;
        }

        // Check if theme already exists
        if ($themeService->themeExists($themeName)) {
            $this->error("Theme '{$themeName}' already exists.");
            return 1;
        }

        // Validate copy-from theme if provided
        if ($copyFrom && !$themeService->themeExists($copyFrom)) {
            $this->error("Source theme '{$copyFrom}' does not exist.");
            return 1;
        }

        $this->info("Creating theme '{$themeName}'...");

        try {
            $success = $themeService->createTheme($themeName, $copyFrom);

            if (!$success) {
                $this->error('Failed to create theme.');
                return 1;
            }

            $this->info("Theme '{$themeName}' created successfully!");

            if ($copyFrom) {
                $this->info("Theme was copied from '{$copyFrom}'.");
            }

            $this->info('Theme location: ' . resource_path("themes/{$themeName}"));

            // Ask if user wants to activate the new theme
            if ($this->confirm('Would you like to activate this theme now?', false)) {
                $themeService->activateTheme($themeName);
                $this->info("Theme '{$themeName}' has been activated!");
            }

            // Show next steps
            $this->line('');
            $this->line('Next steps:');
            $this->line("- Edit theme.json to customize theme settings");
            $this->line("- Modify style.css to add custom styles");
            $this->line("- Create view overrides in the views/ directory");
            $this->line("- Add partials in the partials/ directory");
            $this->line("- Run 'php artisan theme:publish {$themeName}' to publish assets");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error creating theme: ' . $e->getMessage());
            return 1;
        }
    }
}
