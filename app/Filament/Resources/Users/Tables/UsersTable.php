<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\Role;
use App\Filament\Resources\Users\Actions\UserRoles;
use App\Models\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->state(fn($record) => $record->avatar_url
                        ? asset(Storage::url($record->avatar_url))
                        : null
                    )
                    ->circular(),
                ToggleColumn::make('is_admin')
                    ->label('Is admin?')
                    ->state(fn(User $record) => $record->hasRole(Role::Admin))
                    ->updateStateUsing(UserRoles::updateStateUsing(...)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
