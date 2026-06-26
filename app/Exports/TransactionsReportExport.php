<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionsReportExport implements FromView
{
    public function __construct(
        protected int|string|null $statusId = null,
        protected int|string|null $reinsurerId = null,
    ) {}

    public function view(): View
    {
        return view('filament.resources.transaction.transactions-report', [
            'transactions' => Transaction::query()
                ->with([
                    'type',
                    'status',
                    'operativeDoc.business.reinsurer',
                    'remmitanceCode',
                ])
                ->withMax('logs as latest_net_amount', 'net_amount')
                ->when($this->statusId, function ($query) {
                    $query->where('transaction_status_id', $this->statusId);
                })
                ->when($this->reinsurerId, function ($query) {
                    $query->whereHas('operativeDoc.business', function ($query) {
                        $query->where('reinsurer_id', $this->reinsurerId);
                    });
                })
                ->orderBy('op_document_id')
                ->orderBy('index')
                ->get(),
        ]);
    }
}