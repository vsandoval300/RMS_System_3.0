<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class TransactionsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Distribution by Status';
    protected ?string $maxHeight = '320px';
    protected int|string|array $columnSpan = 1;

    public ?int    $reinsurerId = null;
    public ?string $dateFrom    = null;
    public ?string $dateTo      = null;

    protected function getData(): array
    {
        $rows = Transaction::query()
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
            ->join('transaction_statuses', 'transactions.transaction_status_id', '=', 'transaction_statuses.id')
            ->when($this->reinsurerId, fn ($q) => $q->where('businesses.reinsurer_id', $this->reinsurerId))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('transactions.due_date', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('transactions.due_date', '<=', $this->dateTo))
            ->whereNull('transactions.deleted_at')
            ->selectRaw('transaction_statuses.transaction_status as status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'labels'   => ['Pending', 'In process', 'Completed'],
            'datasets' => [[
                'data' => [
                    (int) ($rows['Pending']    ?? 0),
                    (int) ($rows['In process'] ?? 0),
                    (int) ($rows['Completed']  ?? 0),
                ],
                'backgroundColor' => [
                    'rgb(148, 163, 184)',
                    'rgb(251, 191, 36)',
                    'rgb(34, 197, 94)',
                ],
                'hoverOffset' => 6,
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
            'cutout'  => '65%',
        ];
    }
}
