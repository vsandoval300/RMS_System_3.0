<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OverdueTransactionsWidget extends Widget
{
    protected string $view = 'filament.widgets.overdue-transactions';
    protected int|string|array $columnSpan = 1;

    public ?int    $reinsurerId = null;
    public ?string $dateFrom    = null;
    public ?string $dateTo      = null;

    public function getOverdueTransactions(): Collection
    {
        return Transaction::query()
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
            ->join('reinsurers', 'businesses.reinsurer_id', '=', 'reinsurers.id')
            ->join('transaction_statuses', 'transactions.transaction_status_id', '=', 'transaction_statuses.id')
            ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
            ->when($this->reinsurerId, fn ($q) => $q->where('businesses.reinsurer_id', $this->reinsurerId))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('transactions.due_date', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('transactions.due_date', '<=', $this->dateTo))
            ->whereNull('transactions.deleted_at')
            ->whereDate('transactions.due_date', '<', now()->toDateString())
            ->where('transaction_statuses.transaction_status', '!=', 'Completed')
            ->select(
                'transactions.id',
                'transactions.index',
                'transactions.due_date',
                'transactions.amount',
                'operative_docs.id as doc_id',
                'reinsurers.short_name as reinsurer',
                'transaction_statuses.transaction_status as status',
                'transaction_types.description as type',
            )
            ->orderBy('transactions.due_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $row->edit_url     = TransactionResource::getUrl('edit', ['record' => $row->id]);
                $row->reference    = $row->doc_id . '-TX' . str_pad($row->index, 2, '0', STR_PAD_LEFT);
                $dueDate           = Carbon::parse($row->due_date)->startOfDay();
                $row->days_overdue = (int) Carbon::today()->diffInDays($dueDate, true);
                return $row;
            });
    }
}
