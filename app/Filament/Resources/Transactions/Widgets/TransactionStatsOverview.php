<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Transactions', Transaction::count())
                ->description('Total in the database')
                ->icon('heroicon-o-arrows-right-left')
                ->color('info'),

            Stat::make('Pending', Transaction::whereHas('status', fn ($query) =>
                $query->where('transaction_status', 'Pending')
            )->count())
                ->description('Pending transactions')
                ->icon('heroicon-o-clock')
                ->color('gray'),

            Stat::make('In Process', Transaction::whereHas('status', fn ($query) =>
                $query->where('transaction_status', 'In process')
            )->count())
                ->description('Transactions in process')
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Completed', Transaction::whereHas('status', fn ($query) =>
                $query->where('transaction_status', 'Completed')
            )->count())
                ->description('Completed transactions')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}