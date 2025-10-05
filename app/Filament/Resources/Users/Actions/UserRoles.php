<?php

namespace App\Filament\Resources\Users\Actions;

use App\Enums\Role;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class UserRoles
{
    protected const string Action_ID = 'toggleAdmin';

    public static function toggleAdminAction(): Action
    {
        return Action::make(static::Action_ID)
            ->action(static::handleAction(...))
            ->requiresConfirmation()
            ->extraAttributes(['class' => 'hidden']);
    }

    public static function updateStateUsing(Page $livewire, User $record, $state): void
    {
        $livewire->mountAction(static::Action_ID, [
            'record' => $record,
            'state' => $state,
        ]);
    }

    public static function handleAction(Page $livewire, Action $action, array $arguments)
    {
        $state = data_get($arguments, 'state');

        /** @var User $record */
        $record = $arguments['record'];

        if (auth()->user()->is($record)) {
            Notification::make()
                ->title('You cannot change your own admin status.')
                ->danger()
                ->send();

            $livewire->unmountAction();

            $action->halt();
        }

        if ($state === true) {
            $record->roles()->updateOrCreate(['role' => Role::Admin]);
        } else {
            $record->roles()->where('role', Role::Admin)->delete();
        }
    }
}
