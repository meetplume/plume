<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Models\Comment;
use App\Filament\Resources\Comments\CommentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label(__('Approve'))
                ->visible(fn(Comment $record) => $record->approved_at === null)
                ->color(Color::Green)
                ->icon(Heroicon::Check)
                ->action(fn(Comment $record) => $record->update(['approved_at' => now()])),
            Action::make('disapprove')
                ->label(__('Disapprove'))
                ->visible(fn(Comment $record) => $record->approved_at !== null)
                ->color(Color::Red)
                ->icon(Heroicon::XMark)
                ->action(fn(Comment $record) => $record->update(['approved_at' => null])),
            DeleteAction::make(),
        ];
    }
}
