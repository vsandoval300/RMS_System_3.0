<?php

namespace App\Filament\Resources\StaticsDashboardResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\LoginLog;
use function Symfony\Component\Clock\now;

class UserStatistics extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected function getStats(): array
    {
        // $onlineUsers = User::where('updated_at', '>=', now()->modify('-5 minutes'))->count();
        // return [
        //     Stat::make('Unique views', '192.1k'),
        //     Stat::make('Bounce rate', '21%'),
        //     Stat::make('Average time on page', '3:12'),
        //     Stat::make('Users Online', $onlineUsers)
        //         ->description('Users active in the last 5 minutes')
        //         ->descriptionIcon('heroicon-m-user-group')
        //         ->color('success'),
        // ];

       
        $loginsToday = LoginLog::whereDate('logged_in_at', today())->count();

        $loginsUnique = LoginLog::whereDate('logged_in_at', today())
            ->distinct('user_id')
            ->count('user_id');

        
        return [
            Stat::make('Logins Today', $loginsToday)
                ->description('Successful logins today')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('primary'),
            Stat::make('lLogins Unique', $loginsUnique)
                ->description('Successful logins today')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('primary'),
            Stat::make('Bounce rate', '21%'),
            Stat::make('Average time on page', '3:12'),
        ];
    }
}
