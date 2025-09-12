<?php

namespace App\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Action;
use App\Services\ThemeService;
use Filament\Notifications\Notification;
use Livewire\Component;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class ThemeSelector extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public string $activeTheme;
    public ?string $demoUrl = null;

    public function mount(?string $demoUrl = null)
    {
        $this->demoUrl = $demoUrl ?? '/';
        $this->activeTheme = app(ThemeService::class)->getActiveTheme();
    }

    public function getThemes(): array
    {
        $themeService = app(ThemeService::class);
        $availableThemes = $themeService->getAvailableThemes();

        $themes = [];
        foreach ($availableThemes as $themeName => $config) {
            $screenshotPath = resource_path("themes/{$themeName}/screenshot.png");
            $screenshotUrl = file_exists($screenshotPath)
                ? asset("themes/{$themeName}/screenshot.png")
                : null;

            $themes[$themeName] = [
                'name' => $config['name'] ?? ucfirst($themeName),
                'description' => $config['description'] ?? '',
                'version' => $config['version'] ?? '1.0.0',
                'screenshot' => $screenshotUrl,
                'config' => $config,
            ];
        }

        return $themes;
    }

    public function activateTheme(string $theme): void
    {
        $themeService = app(ThemeService::class);

        if (!$themeService->themeExists($theme)) {
            Notification::make()
                ->title('Theme not found!')
                ->body("The '{$theme}' theme does not exist.")
                ->danger()
                ->send();
            return;
        }

        $success = $themeService->activateTheme($theme);

        if ($success) {
            $this->activeTheme = $theme;

            $themeConfig = $themeService->getThemeConfig();
            $themeName = $themeConfig['name'] ?? ucfirst($theme);

            Notification::make()
                ->title('Theme activated successfully!')
                ->body("The '{$themeName}' theme has been activated.")
                ->success()
                ->send();

            // Refresh the page to show the new theme
            $this->redirect(request()->header('Referer') ?: '/admin/settings/design');
        } else {
            Notification::make()
                ->title('Failed to activate theme!')
                ->body("Could not activate the '{$theme}' theme.")
                ->danger()
                ->send();
        }
    }

    public function previewTheme(string $theme): void
    {
        // For now, just open the demo URL in a new tab
    }

    public function uploadTheme(string $filePath): void
    {
        $themeService = app(ThemeService::class);

        try {
            $result = $themeService->uploadAndInstallTheme($filePath);

            if ($result['success']) {
                Notification::make()
                    ->title('Theme uploaded successfully!')
                    ->body("The '{$result['theme_name']}' theme has been installed and is now available.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Theme upload failed!')
                    ->body($result['message'] ?? 'An error occurred while uploading the theme.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Theme upload failed!')
                ->body('An unexpected error occurred: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function previewThemeAction(): Action
    {
        return Action::make("previewTheme")
            ->label(__('Demo'))
            ->color('gray')
            ->icon(Phosphor::Eye)
            ->url('/', shouldOpenInNewTab: true);
    }

    public function activateThemeAction(): Action
    {
        return Action::make("activateTheme")
            ->label(fn(array $arguments) => $this->activeTheme === $arguments['theme'] ? __('Active') : __('Activate'))
            ->color(fn(array $arguments) => $this->activeTheme === $arguments['theme'] ? 'success' : 'primary')
            ->disabled(fn(array $arguments) => $this->activeTheme === $arguments['theme'])
            ->icon(fn(array $arguments) => $this->activeTheme === $arguments['theme'] ? Phosphor::Check : Phosphor::Lightning)
            ->requiresConfirmation()
            ->modalHeading('Activate Theme')
            ->modalDescription('Are you sure you want to activate the selected theme?')
            ->modalSubmitActionLabel('Activate Theme')
            ->action(function (array $arguments) {
                $this->activateTheme($arguments['theme']);
            });
    }

    public function uploadThemeAction(): Action
    {
        return Action::make('uploadTheme')
            ->label(__('Upload Theme'))
            ->modalHeading(__('Upload Theme'))
            ->modalDescription(__('Upload a ZIP file containing a theme. The theme must include a theme.json file.'))
            ->modalSubmitActionLabel(__('Upload Theme'))
            ->form([
                \Filament\Forms\Components\FileUpload::make('theme_file')
                    ->label(__('Theme ZIP File'))
                    ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                    ->maxSize(10240) // 10MB max
                    ->required()
                    ->disk('local')
                    ->directory('temp-themes')
                    ->helperText(__('Upload a ZIP file containing your theme files. Maximum size: 10MB')),
            ])
            ->action(function (array $data) {
                $this->uploadTheme($data['theme_file']);
            });
    }

    public function render()
    {
        return view('livewire.theme-selector', [
            'themes' => $this->getThemes(),
        ]);
    }
}
