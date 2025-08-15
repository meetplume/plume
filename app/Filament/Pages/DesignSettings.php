<?php

namespace App\Filament\Pages;

use App\Enums\Font;
use Phiki\Theme\Theme;
use App\Enums\SiteSettings;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use App\Support\AvailableLanguages;
use Filament\Support\Icons\Heroicon;
use Rawilk\Settings\Support\Context;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Livewire;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Forms\Components\FontPicker;
use Awcodes\Palette\Forms\Components\ColorPicker as PaletteColorPicker;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Concerns\HandlesSettingsForm;

class DesignSettings extends Page implements HasForms
{
    use InteractsWithForms, HandlesSettingsForm;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-paint-brush';
    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Design';
    protected static ?string $slug = 'settings/design';
    protected static ?string $title = 'Design Settings';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.settings';

    protected function getSettings(): array
    {
        return [
            SiteSettings::PRIMARY_COLOR,
            SiteSettings::NEUTRAL_COLOR,
            SiteSettings::HEADING_FONT,
            SiteSettings::BODY_FONT,
            SiteSettings::CODE_FONT,
            SiteSettings::CODE_THEME,
            SiteSettings::POST_DEFAULT_IMAGE,
        ];
    }

    protected function getSuccessMessage(): string
    {
        return __('Design settings saved!');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading(__('Design'))
                    ->description(__("Give it a fresh coat of paint."))
                    ->icon(Heroicon::OutlinedPaintBrush)
                    ->aside()
                    ->schema([

                        ColorPicker::make(SiteSettings::PRIMARY_COLOR->value)
                            ->helperText(__('Primary accent color used for links, buttons, etc.')),

                        PaletteColorPicker::make(SiteSettings::NEUTRAL_COLOR->value)
                            ->label(__('Neutral color'))
                            ->colors([
                                'stone' => Color::Stone,
                                'neutral' => Color::Neutral,
                                'zinc' => Color::Zinc,
                                'gray' => Color::Gray,
                                'slate' => Color::Slate,
                            ])->storeAsKey()
                            ->helperText(__('What shade of gray would you use? This color is used for backgrounds, borders.')),

                        FontPicker::make(SiteSettings::HEADING_FONT->value),
                        FontPicker::make(SiteSettings::BODY_FONT->value),
                        FontPicker::make(SiteSettings::CODE_FONT->value),

                        Select::make(SiteSettings::CODE_THEME->value)
                            ->options(collect(Theme::cases())->mapWithKeys(fn(Theme $theme) => [
                                $theme->value => $theme->name,
                            ]))
                            ->searchable(),

                    ])->columns(1),

                Section::make()
                    ->heading(__('Post default image'))
                    ->description(__('The default image to use for posts that do not have an image set.'))
                    ->icon(Heroicon::OutlinedPhoto)
                    ->aside()
                    ->schema([
                        FileUpload::make(SiteSettings::POST_DEFAULT_IMAGE->value)
                            ->hiddenLabel()
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                    ])->columns(1),

            ])->statePath('data');
    }
}
