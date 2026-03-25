<?php

namespace App\Filament\User\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UsersLoginsChart extends ChartWidget
{
    protected static ?string $heading = 'User Logins Distribution';

    protected function getData(): array
    {
        $users = User::withCount('loginLogs')
            ->orderByDesc('login_logs_count')
            ->take(10) // 🔥 top 10 para que no se vea saturado
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Logins',
                    'data' => $users->pluck('login_logs_count'),
                    'backgroundColor' => [
                        '#6366F1',
                        '#22C55E',
                        '#F59E0B',
                        '#EF4444',
                        '#3B82F6',
                        '#10B981',
                        '#F97316',
                        '#8B5CF6',
                        '#EC4899',
                        '#14B8A6',
                    ],
                ],
            ],
            'labels' => $users->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // 👈 donut chart
    }
}