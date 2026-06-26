<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use App\Models\TransactionLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsCards extends StatsOverviewWidget
{
    public ?int    $reinsurerId = null;
    public ?string $dateFrom    = null;
    public ?string $dateTo      = null;

    protected int|string|array $columnSpan = 'full';

    private function sumLatestNetAmount(\Illuminate\Database\Eloquent\Builder $query): float
    {
        $ids = (clone $query)->pluck('transactions.id');

        if ($ids->isEmpty()) {
            return 0.0;
        }

        $latestIndex = TransactionLog::query()
            ->select('transaction_id')
            ->selectRaw('MAX("index") as max_idx')
            ->whereIn('transaction_id', $ids)
            ->whereNull('deleted_at')
            ->groupBy('transaction_id');

        return (float) TransactionLog::query()
            ->joinSub($latestIndex, 'latest', function ($join) {
                $join->on('transaction_logs.transaction_id', '=', 'latest.transaction_id')
                     ->on('transaction_logs.index', '=', 'latest.max_idx');
            })
            ->whereNull('transaction_logs.deleted_at')
            ->sum('transaction_logs.net_amount');
    }

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

        $pendingQuery    = $this->baseQuery()->where('transaction_statuses.transaction_status', 'Pending');
        $pendingCount    = (clone $pendingQuery)->count();
        $pendingAmount   = $this->sumLatestNetAmount(clone $pendingQuery);

        $inProcessQuery  = $this->baseQuery()->where('transaction_statuses.transaction_status', 'In process');
        $inProcessCount  = (clone $inProcessQuery)->count();
        $inProcessAmount = $this->sumLatestNetAmount(clone $inProcessQuery);

        $completedQuery  = $this->baseQuery()->where('transaction_statuses.transaction_status', 'Completed');
        $completedCount  = (clone $completedQuery)->count();
        $completedAmount = $this->sumLatestNetAmount(clone $completedQuery);

        $overdueQuery  = $this->baseQuery()
            ->whereDate('transactions.due_date', '<', now()->toDateString())
            ->where('transaction_statuses.transaction_status', '!=', 'Completed');
        $overdueCount  = (clone $overdueQuery)->count();
        $overdueAmount = $this->sumLatestNetAmount(clone $overdueQuery);

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
