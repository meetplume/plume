<?php

namespace App\Console\Commands;

use App\Services\ThemeService;
use Illuminate\Console\Command;

class ThemePublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:publish {theme? : The theme name to publish (publishes all if not specified)} {--force : Force republish even if assets exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish theme assets to the public directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $themeName = $this->argument('theme');
        $force = $this->option('force');
        $themeService = app(ThemeService::class);

        if ($themeName) {
            // Publish specific theme
            return $this->publishTheme($themeName, $themeService, $force);
        } else {
            // Publish all themes
            return $this->publishAllThemes($themeService, $force);
        }
    }

    private function publishTheme(string $themeName, ThemeService $themeService, bool $force): int
    {
        if (!$themeService->themeExists($themeName)) {
            $this->error("Theme '{$themeName}' does not exist.");
            return 1;
        }

        $sourcePath = resource_path("themes/{$themeName}/assets");
        $targetPath = public_path("themes/{$themeName}");

        if (!is_dir($sourcePath)) {
            $this->warn("Theme '{$themeName}' has no assets to publish.");
            return 0;
        }

        if (!$force && is_dir($targetPath)) {
            if (!$this->confirm("Assets for theme '{$themeName}' already exist. Overwrite?", false)) {
                $this->info('Publishing cancelled.');
                return 0;
            }
        }

        $this->info("Publishing assets for theme '{$themeName}'...");

        try {
            $success = $themeService->publishThemeAssets($themeName);

            if (!$success) {
                $this->error('Failed to publish theme assets.');
                return 1;
            }

            $this->info("Assets for theme '{$themeName}' published successfully!");
            $this->line("Assets published to: {$targetPath}");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error publishing theme assets: ' . $e->getMessage());
            return 1;
        }
    }

    private function publishAllThemes(ThemeService $themeService, bool $force): int
    {
        $themes = $themeService->getAvailableThemes();

        if (empty($themes)) {
            $this->warn('No themes found to publish.');
            return 0;
        }

        $this->info('Publishing assets for all themes...');

        $published = 0;
        $errors = 0;

        foreach (array_keys($themes) as $themeName) {
            $sourcePath = resource_path("themes/{$themeName}/assets");

            if (!is_dir($sourcePath)) {
                $this->line("  - {$themeName}: No assets to publish");
                continue;
            }

            $targetPath = public_path("themes/{$themeName}");

            if (!$force && is_dir($targetPath)) {
                $this->line("  - {$themeName}: Assets already exist (use --force to overwrite)");
                continue;
            }

            try {
                if ($themeService->publishThemeAssets($themeName)) {
                    $this->line("  - {$themeName}: Published successfully");
                    $published++;
                } else {
                    $this->line("  - {$themeName}: Failed to publish");
                    $errors++;
                }
            } catch (\Exception $e) {
                $this->line("  - {$themeName}: Error - " . $e->getMessage());
                $errors++;
            }
        }

        $this->line('');
        $this->info("Published assets for {$published} theme(s).");

        if ($errors > 0) {
            $this->warn("{$errors} theme(s) had errors during publishing.");
            return 1;
        }

        return 0;
    }
}
