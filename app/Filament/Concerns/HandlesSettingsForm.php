<?php

namespace App\Filament\Concerns;

use App\Enums\SiteSettings;
use Filament\Actions\SelectAction;
use App\Support\AvailableLanguages;
use App\Services\ThemeFieldsService;
use Filament\Notifications\Notification;

trait HandlesSettingsForm
{
    public ?array $data = [];
    public ?string $language;

    /**
     * Get the settings array for this page.
     * Must be implemented by classes using this trait.
     */
    abstract protected function getSettings(): array;

    /**
     * Get the success message for this page.
     * Must be implemented by classes using this trait.
     */
    abstract protected function getSuccessMessage(): string;

    protected function getHeaderActions(): array {
        return [
            SelectAction::make('language')
                ->label(__('Change language'))
                ->options(AvailableLanguages::availableOptions(simple: true))
        ];
    }

    public function updatedLanguage(): void
    {
        $this->form->fill($this->loadFormData());
    }

    protected function loadFormData(): array
    {
        $formData = [];
        $settings = $this->getSettings();

        foreach ($settings as $setting) {
            if ($setting instanceof SiteSettings) {
                if ($setting->is_translatable() && $this->language) {
                    $formData[$setting->value] = $setting->get(context: ['language' => $this->language]);
                } else {
                    $formData[$setting->value] = $setting->get(context: []);
                }
            }
            elseif(str_starts_with($setting, 'mail_')) {
                $formData[$setting] = config('mail.' . str_replace('mail_', '', $setting));
            }
            elseif(str_starts_with($setting, 'theme_')) {
                $formData[$setting] = settings($setting, theme()->fields($setting)->default());
            }
        }

        return $formData;
    }

    public function mount(): void
    {
        $this->language = SiteSettings::DEFAULT_LANGUAGE->get();
        $this->form->fill($this->loadFormData());
    }

    public function create(): void
    {
        $state = $this->form->getState();
        $settings = $this->getSettings();

        // Handle special logic for primary color cache invalidation
        if (in_array(SiteSettings::PRIMARY_COLOR, $settings)) {
            $oldPrimaryColor = SiteSettings::PRIMARY_COLOR->get();
            $newPrimaryColor = $state[SiteSettings::PRIMARY_COLOR->value] ?? null;
            if ($oldPrimaryColor !== $newPrimaryColor) {
                cache()->forget("primary_palette_generated");
            }
        }

        foreach ($settings as $setting) {
            if ($setting instanceof SiteSettings) {
                if ($setting->is_translatable() && $this->language) {
                    $setting->set($state[$setting->value] ?? null, ['language' => $this->language]);
                } else {
                    $setting->set($state[$setting->value] ?? null);
                }
            }
            else {
                settings()->set($setting, $state[$setting]);
            }
        }

        Notification::make()->success()->title($this->getSuccessMessage())->send();
    }
}
