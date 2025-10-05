<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\Role;
use Filament\Facades\Filament;
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
                ImageColumn::make('avatar_url')
                    ->label('')
                    ->state(fn(User $record) => $record->getFilamentAvatarUrl())
                    ->defaultImageUrl(fn ($record): string => app(Filament::getDefaultAvatarProvider())->get($record))
                    ->grow(false)
                    ->imageSize(32)
                    ->circular(),
                TextColumn::make('name')
                    ->grow()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
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
