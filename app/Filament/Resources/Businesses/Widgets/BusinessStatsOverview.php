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
    #[Reactive]
    public ?array $tableFilters = null;

    #[Reactive]
    public ?string $tableSearch = null;

    protected function getStats(): array
    {
        $query = Business::query()->searchGlobally($this->tableSearch);

        $filters = $this->tableFilters ?? [];

        //Log::info('Widget filters', $this->tableFilters);
        
        $reinsurerId = data_get($filters, 'reinsurer_id.value');
        $interval = data_get($filters, 'date_interval.interval');
        $from = data_get($filters, 'date_interval.from');
        $until = data_get($filters, 'date_interval.until');

        if (filled($reinsurerId)) {
            $query->where('reinsurer_id', $reinsurerId);
        }

        if (filled($interval) && $interval === 'custom') {
            if (filled($from)) {
                $query->whereDate('created_at', '>=', $from);
            }

            if (filled($until)) {
                $query->whereDate('created_at', '<=', $until);
            }
        } elseif (filled($interval)) {
            $query->where('created_at', '>=', now()->subDays((int) $interval));
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
