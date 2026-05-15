<?php

namespace App\Filament\Resources\Businesses\Widgets;

use App\Filament\Resources\Businesses\BusinessResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Business;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;


class BusinessStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static string $resource = BusinessResource::class;

    protected function getStats(): array
    {

        $query =Business::query();

        /*
        |--------------------------------------------------------------------------
        | Apply Filters
        |--------------------------------------------------------------------------
        */

        if ($reinsurer = $this->filters['reinsurer_id'] ?? null) {
            $query->where('reinsurer_id', $reinsurer);
        }

        if ($from = $this->filters['from_date'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $this->filters['to_date'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
        }


        return [
            //
            Stat::make('Total Businesses', (clone $query)->count())
                ->description('Total in the database')
                ->icon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Facultative', (clone $query)->where('reinsurance_type', 'Facultative')->count())
                ->description('Facultative Businesses')
                ->icon('heroicon-o-shield-check')
                ->color('success'),

            Stat::make('Treaty', (clone $query)->where('reinsurance_type', 'Treaty')->count())
                ->description('Treaty Cession Business')
                ->icon('heroicon-o-shield-check')
                ->color('info'),

            Stat::make('In Force', (clone $query)->where('business_lifecycle_status', 'In Force')->count())
                ->description('Currently in force')
                ->icon('heroicon-o-clock')
                ->color('info'),
        ];
    }
}
