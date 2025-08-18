<?php

namespace App\Filament\Pages;

use App\Enums\SiteSettings;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Actions\SelectAction;
use App\Support\AvailableLanguages;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Concerns\HandlesSettingsForm;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms, HandlesSettingsForm;

    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'General';
    protected static ?string $slug = 'settings/general';
    protected static ?string $title = 'General Settings';
    protected static ?int $navigationSort = 0;
    protected string $view = 'filament.pages.settings';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::Sliders->getIconForWeight(PhosphorWeight::Duotone);
    }

    protected function getSettings(): array
    {
        return [
            SiteSettings::SITE_LOGO,
            SiteSettings::FAVICON,
            SiteSettings::SITE_NAME,
            SiteSettings::DISPLAY_SITE_NAME,
            SiteSettings::LANGUAGES,
            SiteSettings::DEFAULT_LANGUAGE,
            SiteSettings::FALLBACK_LANGUAGE,
        ];
    }

    protected function getSuccessMessage(): string
    {
        return __('General settings saved!');
    }

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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading(__('Identity'))
                    ->description(__("Who's this blog for?"))
                    ->icon(Phosphor::IdentificationBadge->getIconForWeight(PhosphorWeight::Duotone))
                    ->aside()
                    ->schema([
                        FileUpload::make(SiteSettings::SITE_LOGO->value)
                            ->label(__('Site logo'))
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor(),

                        FileUpload::make(SiteSettings::FAVICON->value)
                            ->label(__('Site favicon'))
                            ->avatar()
                            ->panelLayout('compact square')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor(),

                        TextInput::make(SiteSettings::SITE_NAME->value)
                            ->label(__('Site name')),

                        Toggle::make(SiteSettings::DISPLAY_SITE_NAME->value)
                            ->label(__('Display site name?')),

                    ])->columns(1),

                Section::make()
                    ->heading(__('Localization'))
                    ->description(__('Imagine having a multilingual website...'))
                    ->icon(Phosphor::Translate->getIconForWeight(PhosphorWeight::Duotone))
                    ->aside()
                    ->schema([
                        Select::make(SiteSettings::LANGUAGES->value)
                            ->label(__('Languages'))
                            ->multiple()
                            ->searchable()
                            ->options(AvailableLanguages::options())
                            ->helperText(__("Choose all the available languages on your blog.")),
                        Select::make(SiteSettings::DEFAULT_LANGUAGE->value)
                            ->label(__('Default language'))
                            ->options(AvailableLanguages::options()),
                        Select::make(SiteSettings::FALLBACK_LANGUAGE->value)
                            ->label(__('Fallback language'))
                            ->options(AvailableLanguages::options()),

                    ])->columns(1),

            ])->statePath('data');
    }
}
