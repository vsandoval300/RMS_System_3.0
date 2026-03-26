<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;

class UsersByDepartmentChart extends ChartWidget
{
    protected static ?string $heading = 'Penetración por departamento (%)';
    protected ?int $height = 300;

    protected function getData(): array
    {
        $rows = DB::select("
            WITH base AS (
                SELECT
                    d.name AS department,
                    COUNT(DISTINCT u.id) AS total_users,
                    COUNT(DISTINCT l.user_id) AS active_users
                FROM departments d
                LEFT JOIN users u ON u.department_id = d.id
                LEFT JOIN login_logs l 
                    ON l.user_id = u.id
                    AND l.logged_in_at >= CURRENT_DATE - INTERVAL '30 days'
                GROUP BY d.name
            )
            SELECT *,
                CASE 
                    WHEN total_users > 0 
                    THEN (active_users::decimal / total_users) * 100
                    ELSE 0
                END AS penetration
            FROM base
            ORDER BY penetration DESC
        ");

        // Top 5 + “Otros”
        $top = array_slice($rows, 0, 5);
        $others = array_slice($rows, 5);

        if (count($others) > 0) {
            $totalUsers = array_sum(array_map(fn($r) => $r->total_users, $others));
            $activeUsers = array_sum(array_map(fn($r) => $r->active_users, $others));

            $top[] = (object)[
                'department' => 'Otros',
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'penetration' => $totalUsers > 0
                    ? ($activeUsers / $totalUsers) * 100
                    : 0,
            ];
        }

        $rows = $top;

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($rows as $row) {
            $penetration = round($row->penetration, 1);
            $data[] = $penetration;
            $labels[] = "{$row->department} ({$row->active_users}/{$row->total_users})";

            $colors[] = match (true) {
                $penetration >= 70 => '#10B981', // verde
                $penetration >= 40 => '#F59E0B', // amarillo
                default => '#EF4444',           // rojo
            };
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Penetración (%)',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // barras horizontales
            'scales' => [
                'x' => [
                    'max' => 100,
                    'ticks' => [
                        'format' => '{value}%',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}