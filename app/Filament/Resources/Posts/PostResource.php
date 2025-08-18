<?php

namespace App\Filament\Resources\Posts;

use App\Enums\SiteSettings;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class PostResource extends Resource
{
    use Translatable;

    protected static ?string $model = Post::class;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::Article->getIconForWeight(PhosphorWeight::Duotone);
    }

    public static function getTranslatableLocales(): array
    {
        return SiteSettings::LANGUAGES->get();
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
