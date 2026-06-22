<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsCards extends StatsOverviewWidget
{
    public ?int    $reinsurerId = null;
    public ?string $dateFrom    = null;
    public ?string $dateTo      = null;

    protected int|string|array $columnSpan = 'full';

    private function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Transaction::query()
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
            ->join('transaction_statuses', 'transactions.transaction_status_id', '=', 'transaction_statuses.id')
            ->when($this->reinsurerId, fn ($q) => $q->where('businesses.reinsurer_id', $this->reinsurerId))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('transactions.due_date', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('transactions.due_date', '<=', $this->dateTo))
            ->whereNull('transactions.deleted_at');
    }

    protected function getStats(): array
    {
        $fmt = fn ($n) => '$' . number_format((float) $n, 2, '.', ',');

        $pendingCount    = $this->baseQuery()->where('transaction_statuses.transaction_status', 'Pending')->count();
        $pendingAmount   = (float) $this->baseQuery()->where('transaction_statuses.transaction_status', 'Pending')->sum('transactions.amount');

        $inProcessCount  = $this->baseQuery()->where('transaction_statuses.transaction_status', 'In process')->count();
        $inProcessAmount = (float) $this->baseQuery()->where('transaction_statuses.transaction_status', 'In process')->sum('transactions.amount');

        $completedCount  = $this->baseQuery()->where('transaction_statuses.transaction_status', 'Completed')->count();
        $completedAmount = (float) $this->baseQuery()->where('transaction_statuses.transaction_status', 'Completed')->sum('transactions.amount');

        $overdueCount  = $this->baseQuery()
            ->whereDate('transactions.due_date', '<', now()->toDateString())
            ->where('transaction_statuses.transaction_status', '!=', 'Completed')
            ->count();
        $overdueAmount = (float) $this->baseQuery()
            ->whereDate('transactions.due_date', '<', now()->toDateString())
            ->where('transaction_statuses.transaction_status', '!=', 'Completed')
            ->sum('transactions.amount');

        return [
            Stat::make('Pending', number_format($pendingCount))
                ->description($fmt($pendingAmount))
                ->icon('heroicon-o-clock')
                ->color('gray'),

            Stat::make('In Process', number_format($inProcessCount))
                ->description($fmt($inProcessAmount))
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Completed', number_format($completedCount))
                ->description($fmt($completedAmount))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Overdue', number_format($overdueCount))
                ->description($fmt($overdueAmount))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
