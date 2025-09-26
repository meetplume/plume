<?php

namespace App\Console\Commands;

use App\Services\ThemeService;
use Illuminate\Console\Command;

class ThemeListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:list {--detailed : Show detailed theme information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available themes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $detailed = $this->option('detailed');
        $themeService = app(ThemeService::class);
        $themes = $themeService->getAvailableThemes();
        $activeTheme = $themeService->getActiveTheme();

        if (empty($themes)) {
            $this->warn('No themes found.');
            $this->line('Create a theme using: php artisan make:theme <name>');
            return 0;
        }

        $this->info('Available Themes:');
        $this->line('');

        if ($detailed) {
            $this->showDetailedList($themes, $activeTheme);
        } else {
            $this->showSimpleList($themes, $activeTheme);
        }

        $this->line('');
        $this->line('Commands:');
        $this->line('  php artisan theme:activate <name>  - Activate a theme');
        $this->line('  php artisan theme:publish <name>   - Publish theme assets');
        $this->line('  php artisan make:theme <name>      - Create a new theme');

        return 0;
    }

    private function showSimpleList(array $themes, string $activeTheme): void
    {
        foreach ($themes as $themeName => $config) {
            $status = $themeName === $activeTheme ? '<fg=green>[ACTIVE]</fg=green>' : '';
            $name = $config['name'] ?? ucfirst($themeName);
            $version = $config['version'] ?? 'Unknown';

            $this->line("  <fg=cyan>{$themeName}</fg=cyan> - {$name} (v{$version}) {$status}");
        }
    }

    private function showDetailedList(array $themes, string $activeTheme): void
    {
        foreach ($themes as $themeName => $config) {
            $isActive = $themeName === $activeTheme;

            // Theme header
            $status = $isActive ? '<fg=green>[ACTIVE]</fg=green>' : '<fg=gray>[INACTIVE]</fg=gray>';
            $this->line("<fg=cyan>━━━ {$themeName} {$status} ━━━</fg=cyan>");

            // Basic info
            $this->line("  Name: " . ($config['name'] ?? ucfirst($themeName)));
            $this->line("  Version: " . ($config['version'] ?? 'Unknown'));
            $this->line("  Author: " . ($config['author'] ?? 'Unknown'));

            if (!empty($config['description'])) {
                $this->line("  Description: {$config['description']}");
            }

            // Check for assets
            $assetsPath = resource_path("themes/{$themeName}/assets");
            $publicAssetsPath = public_path("themes/{$themeName}");
            $hasAssets = is_dir($assetsPath);
            $assetsPublished = is_dir($publicAssetsPath);

            if ($hasAssets) {
                $assetStatus = $assetsPublished ? '<fg=green>Published</fg=green>' : '<fg=yellow>Not Published</fg=yellow>';
                $this->line("  Assets: {$assetStatus}");
            } else {
                $this->line("  Assets: <fg=gray>None</fg=gray>");
            }

            // Theme location
            $this->line("  Location: resources/themes/{$themeName}/");

            $this->line('');
        }
    }
}
