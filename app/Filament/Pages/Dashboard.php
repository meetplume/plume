<?php

namespace App\Filament\Pages;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class Dashboard extends \Filament\Pages\Dashboard
{
    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null {
        return Phosphor::House->getIconForWeight(PhosphorWeight::Duotone);
    }
}
