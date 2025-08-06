<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ActionGroup;
use Filament\Support\Colors\Color;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;

class CommentsTable
{
    /**
     * @throws \Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content')
                    ->label(__('Comment'))
                    ->wrap()
                    ->grow()
                    ->fontFamily(FontFamily::Mono)
                    ->formatStateUsing(fn($state) => str($state)->markdown()->stripTags())
                    ->limit(40),
                TextColumn::make('author.name')
                    ->grow(false)
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('post.title')
                    ->grow(false)
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->grow(false)
                    ->dateTimeTooltip()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('Approve'))
                    ->visible(fn($record) => $record->approved_at === null)
                    ->color(Color::Green)
                    ->icon(Heroicon::Check)
                    ->action(fn($record) => $record->update(['approved_at' => now()])),
                Action::make('disapprove')
                    ->label(__('Disapprove'))
                    ->visible(fn($record) => $record->approved_at !== null)
                    ->color(Color::Red)
                    ->icon(Heroicon::XMark)
                    ->action(fn($record) => $record->update(['approved_at' => null])),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('approve')
                    ->label(__('Approve'))
                    ->color(Color::Green)
                    ->icon(Heroicon::Check)
                    ->action(fn($records) => $records->each(fn(Comment $record) => $record->update(['approved_at' => now()]))),
                    BulkAction::make('disapprove')
                    ->label(__('Disapprove'))
                    ->color(Color::Red)
                    ->icon(Heroicon::XMark)
                    ->action(fn($records) => $records->each(fn(Comment $record) => $record->update(['approved_at' => null]))),
                ]),
            ]);
    }
}
