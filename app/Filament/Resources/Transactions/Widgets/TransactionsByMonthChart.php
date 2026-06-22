<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TransactionsByMonthChart extends ChartWidget
{
    protected ?string $heading = 'Amount by Month & Status';
    protected ?string $maxHeight = '320px';
    protected int|string|array $columnSpan = 1;

    public ?int    $reinsurerId = null;
    public ?string $dateFrom    = null;
    public ?string $dateTo      = null;

    protected function getData(): array
    {
        // Determine month range: use filter dates if set, otherwise last 12 months
        $from = $this->dateFrom
            ? Carbon::parse($this->dateFrom)->startOfMonth()
            : now()->subMonths(11)->startOfMonth();

        $to = $this->dateTo
            ? Carbon::parse($this->dateTo)->endOfMonth()
            : now()->endOfMonth();

        // Build list of Y-m labels between from and to
        $months = collect();
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $months->push($cursor->format('Y-m'));
            $cursor->addMonth();
        }

        $rows = Transaction::query()
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
            ->join('transaction_statuses', 'transactions.transaction_status_id', '=', 'transaction_statuses.id')
            ->when($this->reinsurerId, fn ($q) => $q->where('businesses.reinsurer_id', $this->reinsurerId))
            ->whereNull('transactions.deleted_at')
            ->whereDate('transactions.due_date', '>=', $from->toDateString())
            ->whereDate('transactions.due_date', '<=', $to->toDateString())
            ->selectRaw("TO_CHAR(transactions.due_date, 'YYYY-MM') as month, transaction_statuses.transaction_status as status, SUM(transactions.amount) as total")
            ->groupBy('month', 'status')
            ->get()
            ->groupBy('status');

        $statusColors = [
            'Pending'    => 'rgb(148, 163, 184)',
            'In process' => 'rgb(251, 191, 36)',
            'Completed'  => 'rgb(34, 197, 94)',
        ];

        $datasets = [];
        foreach (['Pending', 'In process', 'Completed'] as $status) {
            $data    = $rows->get($status, collect());
            $amounts = $months->map(fn ($m) => (float) ($data->firstWhere('month', $m)?->total ?? 0))->values()->all();

            $datasets[] = [
                'label'           => $status,
                'data'            => $amounts,
                'backgroundColor' => $statusColors[$status],
                'borderRadius'    => 4,
            ];
        }

        return [
            'labels'   => $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->all(),
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => [
                    'stacked'     => true,
                    'beginAtZero' => true,
                    'grid'        => ['color' => 'rgba(156,163,175,0.15)'],
                ],
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
