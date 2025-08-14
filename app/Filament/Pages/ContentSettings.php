<?php

namespace App\Filament\Pages;

use App\Enums\MainPages;
use App\Enums\SiteSettings;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use App\Support\AvailableLanguages;
use Filament\Support\Icons\Heroicon;
use Rawilk\Settings\Support\Context;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Concerns\HandlesSettingsForm;

class ContentSettings extends Page implements HasForms
{
    use InteractsWithForms, HandlesSettingsForm;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-computer-desktop';
    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Content';
    protected static ?string $slug = 'settings/content';
    protected static ?string $title = 'Content Settings';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.settings';

    protected function getSettings(): array
    {
        return [
            SiteSettings::CONTACT_EMAIL,
            SiteSettings::HERO_TITLE,
            SiteSettings::HERO_SUBTITLE,
            SiteSettings::HERO_IMAGE,
            SiteSettings::HERO_IMAGE_HEIGHT,
            SiteSettings::HERO_IMAGE_FULL_WIDTH,
            SiteSettings::ABOUT_IMAGE,
            SiteSettings::ABOUT_TEXT,
            SiteSettings::ABOUT_TITLE,
            SiteSettings::ABOUT_IMAGE_CIRCULAR,
            SiteSettings::ABOUT_IMAGE_WIDTH,
            SiteSettings::ABOUT_IMAGE_HEIGHT,
            SiteSettings::FOOTER_TEXT,
            SiteSettings::COPYRIGHT_TEXT,
            SiteSettings::PERMALINKS,
        ];
    }

    protected function getSuccessMessage(): string
    {
        return __('Content settings saved!');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading(__('Contact'))
                    ->description(__("A bit more about you."))
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->aside()
                    ->schema([

                        TextInput::make(SiteSettings::CONTACT_EMAIL->value)
                            ->label(__('Contact email'))
                            ->email()
                            ->prefixIcon(Heroicon::OutlinedEnvelope),

                        // TODO: Add github, x, bluesky, linkedin

                    ])->columns(1),

                Section::make()
                    ->heading(__('Hero'))
                    ->description(__('Details for the hero section on home page.'))
                    ->icon(Heroicon::OutlinedStar)
                    ->aside()
                    ->schema([
                        TextInput::make(SiteSettings::HERO_TITLE->value)
                            ->live(onBlur: true)
                            ->label(__('Hero title'))
                            ->helperText(__('Wrap a word between two pipes to make it colored. E.g. "Welcome to my |personal| blog"')),

                        TextInput::make(SiteSettings::HERO_SUBTITLE->value)
                            ->live(onBlur: true)
                            ->label(__('Hero subtitle')),

                        FileUpload::make(SiteSettings::HERO_IMAGE->value)
                            ->label(__('Hero image'))
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor(),

                        TextInput::make(SiteSettings::HERO_IMAGE_HEIGHT->value)
                            ->label(__('Image height'))
                            ->suffix('px')
                            ->numeric()
                            ->helperText(__('Default: ') . SiteSettings::HERO_IMAGE_HEIGHT->getDefaultValue() . 'px'),

                        Toggle::make(SiteSettings::HERO_IMAGE_FULL_WIDTH->value)
                            ->label(__('Full width?')),
                    ])->columns(1),

                Section::make()
                    ->heading(__('About'))
                    ->description(__('Details for the about section.'))
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->aside()
                    ->schema([
                        FileUpload::make(SiteSettings::ABOUT_IMAGE->value)
                            ->label(__('About image'))
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor(),

                        TextInput::make(SiteSettings::ABOUT_IMAGE_WIDTH->value)
                            ->label(__('Image width'))
                            ->suffix('px')
                            ->numeric()
                            ->helperText(__('Default: ') . SiteSettings::ABOUT_IMAGE_WIDTH->getDefaultValue() . 'px'),

                        TextInput::make(SiteSettings::ABOUT_IMAGE_HEIGHT->value)
                            ->label(__('Image height'))
                            ->suffix('px')
                            ->numeric()
                            ->helperText(__('Default: ') . SiteSettings::ABOUT_IMAGE_HEIGHT->getDefaultValue() . 'px'),

                        Toggle::make(SiteSettings::ABOUT_IMAGE_CIRCULAR->value)
                            ->label(__('Circular image?')),

                        TextInput::make(SiteSettings::ABOUT_TITLE->value)
                            ->live(onBlur: true)
                            ->label(__('About title')),

                        RichEditor::make(SiteSettings::ABOUT_TEXT->value)
                            ->live(onBlur: true)
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['h2', 'h3'],
                                ['blockquote', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->label(__('About text')),
                    ])->columns(1),

                Section::make()
                    ->heading(__('Footer'))
                    ->description(__('Fill details for footer and copyright.'))
                    ->icon(Heroicon::ArrowDown)
                    ->aside()
                    ->schema([
                        RichEditor::make(SiteSettings::FOOTER_TEXT->value)
                            ->live(onBlur: true)
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['undo', 'redo'],
                            ])
                            ->label(__('Footer text')),

                        TextInput::make(SiteSettings::COPYRIGHT_TEXT->value)
                            ->live(onBlur: true)
                            ->label(__('Copyright text'))
                            ->helperText(__('Use {year} to display the current year. E.g. ©{year} My Company.')),

                    ])->columns(1),

                Section::make()
                    ->heading(__('Permalinks'))
                    ->description(__('Customize the default slugs for main pages.'))
                    ->icon(Heroicon::OutlinedLink)
                    ->aside()
                    ->schema(function(){
                        $schema = [];
                        foreach (MainPages::cases() as $page) {
                            $schema[] = TextInput::make(SiteSettings::PERMALINKS->value.'.'.$page->value)
                                ->label(__($page->getTitle()));
                        }
                        return $schema;
                    })->columns(1),

            ])->statePath('data');
    }
}
