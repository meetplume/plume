<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\MarkdownEditor;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('author', 'name')
                    ->required(),
                Select::make('post_id')
                    ->relationship('post', 'title')
                    ->required(),
                Select::make('parent_id')
                    ->label(__('Parent Comment'))
                    ->relationship('parent', 'id'),
                MarkdownEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('modified_at'),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
