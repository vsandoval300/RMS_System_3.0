<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;


class UserStatistics extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $data = DB::selectOne("
            WITH today AS (
                SELECT 
                    COUNT(*) AS logins_today,
                    COUNT(DISTINCT user_id) AS unique_today
                FROM login_logs
                WHERE logged_in_at >= CURRENT_DATE
            ),
            active_30 AS (
                SELECT COUNT(DISTINCT user_id) AS active_30d
                FROM login_logs
                WHERE logged_in_at >= CURRENT_DATE - INTERVAL '30 days'
            ),
            totals AS (
                SELECT COUNT(*) AS total_users FROM users
            ),
            yesterday AS (
                SELECT COUNT(*) AS logins_yesterday
                FROM login_logs
                WHERE logged_in_at >= CURRENT_DATE - INTERVAL '1 day'
                AND logged_in_at < CURRENT_DATE
            )
            SELECT
                t.logins_today,
                t.unique_today,
                a.active_30d,
                y.logins_yesterday,
                (tot.total_users - a.active_30d) AS inactive_users,
                tot.total_users
            FROM today t, active_30 a, totals tot, yesterday y
        ");

        $inactivePct = $data->total_users > 0
            ? ($data->inactive_users * 100 / $data->total_users)
            : 0;
        
        $pctChange = $data->logins_yesterday > 0
            ? (($data->logins_today - $data->logins_yesterday) * 100 / $data->logins_yesterday)
            : 0;

        return [
            Stat::make('Logins Today', $data->logins_today)
                ->description(number_format($pctChange, 1) . '% vs yesterday')
                ->color($pctChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-arrow-right-on-rectangle'),
                //->color('primary'),

            Stat::make('Unique Today', $data->unique_today)
                ->description('Unique users today')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Active 30 Days', $data->active_30d)
                ->description('Users active last 30 days')
                ->color('info'),

            Stat::make('Inactive Users', $data->inactive_users)
                ->description(number_format($inactivePct, 1) . '% of total users')
                ->color('danger'),
        ];
    }
}
