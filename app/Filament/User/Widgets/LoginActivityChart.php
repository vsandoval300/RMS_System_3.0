<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LoginActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Login Activity';

    public ?string $filter = '30d';

    protected function getFilters(): ?array
    {
        return [
            '7d' => '7 days',
            '30d' => '30 days',
            '90d' => '90 days',
        ];
    }

    protected function getData(): array
    {
        // 📅 determinar rango
        $days = match ($this->filter) {
            '7d' => 7,
            '90d' => 90,
            default => 30,
        };

        $rows = DB::select("
            WITH dates AS (
                SELECT generate_series(
                    CURRENT_DATE - INTERVAL '{$days} days',
                    CURRENT_DATE,
                    INTERVAL '1 day'
                )::date AS date
            ),
            base AS (
                SELECT
                    DATE(created_at) AS date,
                    COUNT(*) AS logins,
                    COUNT(DISTINCT user_id) AS users
                FROM login_logs
                WHERE created_at >= CURRENT_DATE - INTERVAL '{$days} days'
                GROUP BY date
            )
            SELECT
                d.date,
                COALESCE(b.logins, 0) AS logins,
                COALESCE(b.users, 0) AS users,
                AVG(COALESCE(b.logins, 0)) OVER (
                    ORDER BY d.date
                    ROWS BETWEEN 6 PRECEDING AND CURRENT ROW
                ) AS moving_avg,
                AVG(COALESCE(b.logins, 0)) OVER () AS global_avg
            FROM dates d
            LEFT JOIN base b ON b.date = d.date
            ORDER BY d.date
        ");

        return [
            'datasets' => [
                [
                    'label' => 'Logins',
                    'data' => array_column($rows, 'logins'),
                    'borderColor' => '#3B82F6', // azul
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                    'tension' => 0.3,
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Unique users',
                    'data' => array_column($rows, 'users'),
                    'borderColor' => '#10B981', // verde
                    'backgroundColor' => 'rgba(16,185,129,0.2)',
                    'borderDash' => [5, 5],
                    'tension' => 0.3,
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Tendency (7d)',
                    'data' => array_map(fn($v) => round($v, 2), array_column($rows, 'moving_avg')),
                    'borderColor' => '#F59E0B', // amarillo
                    'borderWidth' => 3,
                    'tension' => 0.4,
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Average',
                    'data' => array_fill(0, count($rows), round($rows[0]->global_avg ?? 0, 2)),
                    'borderColor' => '#6B7280',
                    'borderDash' => [2, 2],
                    'pointRadius' => 0,
                ],
            ],
            'labels' => array_column($rows, 'date'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

}