<?php

namespace App\Filament\Underwritten\Widgets;

use Filament\Widgets\ChartWidget;

class UnderwrittenBusiness extends ChartWidget
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
