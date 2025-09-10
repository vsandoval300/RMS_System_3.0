<?php

namespace App\Filament\Underwritten\Resources\BusinessResource\Widgets;

use Filament\Widgets\ChartWidget;

class PremiumsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
