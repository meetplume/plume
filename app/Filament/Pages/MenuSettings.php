<?php

namespace App\Filament\Pages;

use App\Enums\Iconoir;
use App\Enums\MainPages;
use App\Enums\SiteSettings;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Flex;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Page as CmsPage;
use App\Filament\Concerns\HandlesSettingsForm;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class MenuSettings extends Page implements HasForms
{
    use InteractsWithForms, HandlesSettingsForm;

    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Menus';
    protected static ?string $slug = 'settings/menus';
    protected static ?string $title = 'Menu Settings';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.settings';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::List->getIconForWeight(PhosphorWeight::Duotone);
    }

    protected function getSettings(): array
    {
        return [
            SiteSettings::MAIN_MENU,
            SiteSettings::MAIN_MENU_MORE,
            SiteSettings::FOOTER_MENU,
        ];
    }

    protected function getSuccessMessage(): string
    {
        return __('Menu settings saved!');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading(__('Main Menu'))
                    ->description(__('Your website needs a main menu, right?'))
                    ->icon(Phosphor::List->getIconForWeight(PhosphorWeight::Duotone))
                    ->aside()
                    ->schema([

                        Repeater::make(SiteSettings::MAIN_MENU->value)
                            ->schema([
                                Flex::make([
                                    Select::make('icon')
                                        ->label(__('Icon'))
                                        ->options(
                                            collect(Heroicon::cases())
                                                ->filter(fn (Heroicon $heroicon) => str_starts_with($heroicon->value, 'o-'))
                                                ->mapWithKeys(function (Heroicon $heroicon) {
                                                    $iconName = $heroicon->value;
                                                    $iconHtml = \Filament\Support\generate_icon_html($heroicon)->toHtml();
                                                    $label = "<div class='flex gap-2'>$iconHtml<span style='display: none;'>$iconName</span></div>";
                                                    return [$iconName => $label];
                                                })
                                                ->toArray()
                                        )
                                        ->dehydrateStateUsing(fn ($state) => $state instanceof Heroicon ? $state->value : $state)
                                        ->default(Heroicon::OutlinedHome->value)
                                        ->searchable()
                                        ->preload()
                                        ->allowHtml()
                                        ->required()
                                        ->grow(false),

                                    Select::make('page')
                                        ->label(__('Page'))
                                        ->live()
                                        ->options(function () {
                                            $base = collect(MainPages::cases())->mapWithKeys(fn (MainPages $page) => [$page->value => $page->getTitle()])->toArray();
                                            $cms = CmsPage::query()->orderBy('title')->get()->mapWithKeys(function (CmsPage $p) {
                                                return ['page:' . $p->id => $p->title];
                                            })->toArray();

                                            return [
                                                ...$base,
                                                ...$cms,
                                                'custom' => __('Custom URL'),
                                            ];
                                        }),
                                ]),

                                Group::make([
                                    TextInput::make('name')
                                        ->label(__('Name'))
                                        ->grow()
                                        ->live(onBlur: true),

                                    TextInput::make('url')
                                        ->label(__('URL'))
                                        ->grow(),

                                    Toggle::make('open_in_new_tab')->label(__('Open in new tab?'))->inline(false)->grow(false),
                                ])
                                ->visibleJs(<<<'JS'
                                    $get('page') === 'custom'
                                    JS),
                            ])
                            ->addActionAlignment(Alignment::Start)
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(function (array $state): ?string {
                                $pageId = (int) str($state[ 'page' ])->after('page:')->toString();
                                $pageModel = CmsPage::query()->find($pageId);
                                if ($pageModel) {
                                    return $pageModel->title;
                                }
                                return MainPages::tryFrom($state[ 'page' ] ?? '')?->getTitle() ?? $state[ 'name' ] ?? null;
                            }),

                        Builder::make(SiteSettings::MAIN_MENU_MORE->value)
                            ->label(__('Dropdown Menu'))
                            ->blocks([
                                Builder\Block::make('divider')
                                    ->label(__('Divider'))
                                    ->schema([
                                        TextInput::make('label')->label(__('Label')),
                                    ]),
                                Builder\Block::make('item')
                                    ->label(__('Dropdown Link'))
                                    ->schema([
                                        Flex::make([
                                            Select::make('icon')
                                                ->label(__('Icon'))
                                                ->options([
                                                    'heroicons' =>
                                                    collect(Heroicon::cases())
                                                        ->mapWithKeys(function (Heroicon $heroicon) {
                                                            $iconName = $heroicon->value;
                                                            $iconHtml = \Filament\Support\generate_icon_html($heroicon)->toHtml();
                                                            $label = "<div class='flex gap-2'>$iconHtml<span style='display: none;'>$iconName</span></div>";
                                                            return ["heroicon-" . $iconName => $label];
                                                        })
                                                        ->toArray(),
                                                    'iconoir' =>
                                                    collect(Iconoir::cases())
                                                        ->mapWithKeys(function (Iconoir $iconoir) {
                                                            $iconName = "iconoir-" . $iconoir->value;
                                                            $iconHtml = svg($iconName, ['class' => 'w-6 h-6'])->toHtml();
                                                            $label = "<div class='flex gap-2'>$iconHtml<span style='display: none;'>$iconName</span></div>";
                                                            return [$iconName => $label];
                                                        })
                                                        ->toArray()
                                                    ],
                                                )
                                                ->dehydrateStateUsing(fn ($state) => $state instanceof Heroicon ? $state->value : $state)
                                                ->default(Heroicon::OutlinedHome->value)
                                                ->searchable()
                                                ->preload()
                                                ->allowHtml()
                                                ->required()
                                                ->grow(false),

                                            Select::make('page')
                                                ->label(__('Page'))
                                                ->live()
                                                ->options(function () {
                                                    $base = collect(MainPages::cases())->mapWithKeys(fn (MainPages $page) => [$page->value => $page->getTitle()])->toArray();
                                                    $cms = CmsPage::query()->orderBy('title')->get()->mapWithKeys(function (CmsPage $p) {
                                                        return ['page:' . $p->id => $p->title];
                                                    })->toArray();

                                                    return [
                                                        ...$base,
                                                        ...$cms,
                                                        'custom' => __('Custom URL'),
                                                    ];
                                                }),
                                        ]),

                                        Group::make([
                                            TextInput::make('name')
                                                ->label(__('Name'))
                                                ->grow()
                                                ->live(onBlur: true),

                                            TextInput::make('url')
                                                ->label(__('URL'))
                                                ->grow(),

                                            Toggle::make('open_in_new_tab')->label(__('Open in new tab?'))->inline(false)->grow(false),
                                        ])
                                            ->visibleJs(<<<'JS'
                                            $get('page') === 'custom'
                                            JS),
                                    ]),
                            ])
                            ->addActionAlignment(Alignment::Start)
                            ->collapsible()
                            ->collapsed(),
                    ]),

                    Section::make()
                        ->heading(__('Footer Menu'))
                        ->description(__('Display some links in the footer.'))
                        ->icon(Phosphor::List->getIconForWeight(PhosphorWeight::Duotone))
                        ->aside()
                        ->schema([

                            Repeater::make(SiteSettings::FOOTER_MENU->value)
                                ->schema([
                                    Select::make('page')
                                        ->label(__('Page'))
                                        ->live()
                                        ->options(function () {
                                            $base = collect(MainPages::cases())->mapWithKeys(fn (MainPages $page) => [$page->value => $page->getTitle()])->toArray();
                                            $cms = CmsPage::query()->orderBy('title')->get()->mapWithKeys(function (CmsPage $p) {
                                                return ['page:' . $p->id => $p->title];
                                            })->toArray();

                                            return [
                                                ...$base,
                                                ...$cms,
                                                'custom' => __('Custom URL'),
                                            ];
                                        }),

                                    Group::make([
                                        TextInput::make('name')
                                            ->label(__('Name'))
                                            ->grow()
                                            ->live(onBlur: true),

                                        TextInput::make('url')
                                            ->label(__('URL'))
                                            ->grow(),

                                        Toggle::make('open_in_new_tab')->label(__('Open in new tab?'))->inline(false)->grow(false),
                                    ])
                                        ->visibleJs(<<<'JS'
                                        $get('page') === 'custom'
                                        JS),
                                ])
                                ->addActionAlignment(Alignment::Start)
                                ->collapsible()
                                ->collapsed()
                                ->itemLabel(function (array $state): ?string {
                                    $pageId = (int) str($state[ 'page' ])->after('page:')->toString();
                                    $pageModel = CmsPage::query()->find($pageId);
                                    if ($pageModel) {
                                        return $pageModel->title;
                                    }
                                    return MainPages::tryFrom($state[ 'page' ] ?? '')?->getTitle() ?? $state[ 'name' ] ?? null;
                                }),

                    ])->columns(1),

            ])->statePath('data');
    }
}
