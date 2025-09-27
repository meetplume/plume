<?php

namespace App\Filament\Resources\Comments;

use App\Enums\SiteSettings;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Comments\Pages\CreateComment;
use App\Filament\Resources\Comments\Pages\EditComment;
use App\Filament\Resources\Comments\Pages\ListComments;
use App\Filament\Resources\Comments\Schemas\CommentForm;
use App\Filament\Resources\Comments\Tables\CommentsTable;
use App\Models\Comment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function shouldRegisterNavigation(): bool
    {
        return SiteSettings::COMMENTS_ENABLED->get();
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::Chats->getIconForWeight(PhosphorWeight::Duotone);
    }

    public static function form(Schema $schema): Schema
    {
        return CommentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommentsTable::configure($table);
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
            'index' => ListComments::route('/'),
            'create' => CreateComment::route('/create'),
            'edit' => EditComment::route('/{record}/edit'),
        ];
    }
}
