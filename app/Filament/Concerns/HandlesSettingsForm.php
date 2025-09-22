<?php

namespace App\Filament\Concerns;

use App\Enums\SiteSettings;
use Filament\Actions\Action;
use Filament\Actions\SelectAction;
use App\Support\AvailableLanguages;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Field;
use Rawilk\Settings\Support\Context;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;

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
                settings()->context(new Context([]))->set($setting, $state[$setting]);
            }
        }

        Notification::make()->success()->title($this->getSuccessMessage())->send();
    }

    public static function resetFields(Component $component): void
    {
        $fields = array_map(fn(Field $field) => $field->getStatePath(false), $component->getChildComponents());

        settings()->context(new Context([]))->flush($fields);

        if (in_array(SiteSettings::PRIMARY_COLOR->value, $fields)) {
            cache()->forget("primary_palette_generated");
        }

        Notification::make('settings_flushed')
            ->title(__('Settings flushed'))
            ->body(__('Your settings have been successfully reverted to their default values.'))
            ->success()
            ->send();
    }

    public static function resetFieldsAction(Component $component)
    {
        return Action::make('reset_' . $component->getKey())
            ->label(__('Reset'))
            ->requiresConfirmation()
            ->color('danger')
            ->icon(Heroicon::ArrowPathRoundedSquare)
            ->outlined()
            ->action(fn() => self::resetFields($component));
    }
}
