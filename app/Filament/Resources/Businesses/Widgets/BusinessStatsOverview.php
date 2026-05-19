<?php

namespace App\Filament\Resources\Businesses\Widgets;

use App\Filament\Resources\Businesses\BusinessResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Business;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Tables\Contracts\HasTable;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\Log;

class BusinessStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
        
    protected function getStats(): array
    {
        $query = Business::query();

        $filters = $this->filters ?? [];

        //Log::info('Widget filters', $this->tableFilters);
        
        $reinsurerId = data_get($filters, 'reinsurer_id.value');
        $from = data_get($filters, 'created_at.from');
        $until = data_get($filters, 'created_at.until');

        if (filled($reinsurerId)) {
            $query->where('reinsurer_id', $reinsurerId);
        }

        if (filled($from)) {
            $query->whereDate('created_at', '>=', $from);
        }

        if (filled($until)) {
            $query->whereDate('created_at', '<=', $until);
        }

        return [
            
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
                ->color('gray'),
        ];
    }
}
