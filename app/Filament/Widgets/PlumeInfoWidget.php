<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PlumeInfoWidget extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.plume-info-widget';
}
