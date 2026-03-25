<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\LoginLog;

class LoginActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Actividad de Logins';

    protected function getData(): array
    {
        $data = LoginLog::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->pluck('total','date');

        return [
            'datasets' => [
                [
                    'label' => 'Logins',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}