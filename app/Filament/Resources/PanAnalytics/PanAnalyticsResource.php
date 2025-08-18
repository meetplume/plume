<?php

namespace App\Filament\Resources\PanAnalytics;

use Illuminate\Contracts\Support\Htmlable;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;
use App\Filament\Resources\PanAnalytics\Pages\ListPanAnalytics;
use App\Filament\Resources\PanAnalytics\Tables\PanAnalyticsTable;
use App\Models\PanAnalytics;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PanAnalyticsResource extends Resource
{
    protected static ?string $model = PanAnalytics::class;
    protected static ?string $navigationLabel = 'Stats';
    protected static ?string $slug = 'stats';
    protected static ?string $modelLabel = 'Stats';
    protected static ?int $navigationSort  = 100;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::ChartBar->getIconForWeight(PhosphorWeight::Duotone);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return PanAnalyticsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPanAnalytics::route('/'),
        ];
    }
}
