<?php

namespace App\Filament\Resources\BusinessResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Business;
use Filament\Widgets\StatsOverviewWidget;


class BusinessStatsOverview extends BaseWidget
{
     protected static string $resource = \App\Filament\Resources\BusinessResource::class;

    protected function getStats(): array
    {

        

        return [
            //
            Stat::make('Total Businesses', Business::count())
                ->description('Total en la base de datos')
                ->icon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Facultative', Business::where('reinsurance_type', 'Facultative')->count())
                ->description('Negocios Facultativos')
                ->icon('heroicon-o-shield-check')
                ->color('success'),

            Stat::make('In Force', Business::where('business_lifecycle_status', 'In Force')->count())
                ->description('Actualmente vigentes')
                ->icon('heroicon-o-clock')
                ->color('info'),
        ];
    }
}
