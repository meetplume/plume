<?php

namespace App\Filament\Pages;

use Phiki\Theme\Theme;
use App\Enums\SiteSettings;
use App\Services\ThemeService;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CodeEditor;
use Filament\Schemas\Components\Livewire;
use Filament\Forms\Components\ColorPicker;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use CharlieEtienne\FilamentFontPicker\FontPicker;
use App\Filament\Forms\Components\ImageRadioButton;
use App\Filament\Forms\Components\ThemeSelector;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;
use Awcodes\Palette\Forms\Components\ColorPicker as PaletteColorPicker;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Concerns\HandlesSettingsForm;

class DesignSettings extends Page implements HasForms
{
    use InteractsWithForms, HandlesSettingsForm;

    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Design';
    protected static ?string $slug = 'settings/design';
    protected static ?string $title = 'Design Settings';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.settings';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::PaintBrush->getIconForWeight(PhosphorWeight::Duotone);
    }

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
            SiteSettings::DARK_MODE,
            SiteSettings::ACTIVE_THEME,
            SiteSettings::THEME_CUSTOM_CSS,
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
                    ->heading(__('Theme'))
                    ->description(__('Choose and customize your site theme.'))
                    ->icon(Phosphor::Palette->getIconForWeight(PhosphorWeight::Duotone))
                    ->aside()
                    ->schema([
                        Livewire::make('theme-selector'),
                    ])->columns(1),

                Section::make()
                    ->heading(__('Design'))
                    ->description(__("Give it a fresh coat of paint."))
                    ->icon(Phosphor::PaintBrush->getIconForWeight(PhosphorWeight::Duotone))
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
                    ->heading(__('Custom CSS'))
                    ->description(__('Embrace coding in nocode environments.'))
                    ->icon(Phosphor::CodeBlock->getIconForWeight(PhosphorWeight::Duotone))
                    ->aside()
                    ->schema([
                        CodeEditor::make(SiteSettings::THEME_CUSTOM_CSS->value)
                            ->label(__('Custom CSS'))
                            ->helperText(__('Add custom CSS to override theme styles. This CSS will be loaded after the theme CSS.'))
                            ->language(Language::Css),
                    ])->columns(1),

                Section::make()
                    ->heading(__('Dark mode'))
                    ->description(__('How should the site look in dark mode?'))
                    ->icon(Phosphor::Moon->getIconForWeight(PhosphorWeight::Duotone))
                    ->aside()
                    ->schema([
                        Select::make(SiteSettings::DARK_MODE->value)
                            ->selectablePlaceholder(false)
                            ->options([
                                'switcher' => __('Switcher (adds a button in header menu)'),
                                'always_dark' => __('Always Dark'),
                                'always_light' => __('Always Light'),
                                'always_system' => __('Always System default'),
                            ])
                    ])->columns(1),

                Section::make()
                    ->heading(__('Post default image'))
                    ->description(__('The default image to use for posts that do not have an image set.'))
                    ->icon(Phosphor::Image->getIconForWeight(PhosphorWeight::Duotone))
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
