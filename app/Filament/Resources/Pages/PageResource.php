<?php

namespace App\Filament\Resources\Pages;

use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Filament\Resources\Pages\Tables\PagesTable;
use App\Enums\SiteSettings;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class PageResource extends Resource
{
    use Translatable;

    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::BookOpenText->getIconForWeight(PhosphorWeight::Duotone);
    }

    public static function getTranslatableLocales(): array
    {
        return SiteSettings::LANGUAGES->get();
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
