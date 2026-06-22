<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CompletionTrendChart extends ChartWidget
{
    protected ?string $heading = 'Weekly Completion Trend (last 12 weeks)';
    protected ?string $maxHeight = '320px';
    protected int|string|array $columnSpan = 1;

    public ?int    $reinsurerId = null;
    public ?string $dateFrom    = null;
    public ?string $dateTo      = null;

    protected function getData(): array
    {
        $weekStart = now()->startOfWeek()->subWeeks(11);
        $weekEnd   = now()->endOfWeek();

        // Override with date filter if provided
        if ($this->dateFrom) {
            $weekStart = Carbon::parse($this->dateFrom)->startOfWeek();
        }
        if ($this->dateTo) {
            $weekEnd = Carbon::parse($this->dateTo)->endOfWeek();
        }

        // Build list of week labels (ISO week strings YYYY-WW)
        $weeks = collect();
        $cursor = $weekStart->copy()->startOfWeek();
        while ($cursor->lte($weekEnd)) {
            $weeks->push($cursor->format('Y-W'));   // e.g. "2026-23"
            $cursor->addWeek();
        }

        $base = Transaction::query()
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
            ->join('transaction_statuses', 'transactions.transaction_status_id', '=', 'transaction_statuses.id')
            ->when($this->reinsurerId, fn ($q) => $q->where('businesses.reinsurer_id', $this->reinsurerId))
            ->whereNull('transactions.deleted_at')
            ->whereDate('transactions.due_date', '>=', $weekStart->toDateString())
            ->whereDate('transactions.due_date', '<=', $weekEnd->toDateString())
            ->selectRaw("TO_CHAR(transactions.due_date, 'IYYY-IW') as week, transaction_statuses.transaction_status as status, COUNT(*) as count")
            ->groupBy('week', 'status')
            ->get()
            ->groupBy('status');

        $completedData  = $base->get('Completed', collect());
        $inProcessData  = $base->get('In process', collect());

        $completedCounts = $weeks->map(fn ($w) => (int) ($completedData->firstWhere('week', $w)?->count ?? 0))->values()->all();
        $inProcessCounts = $weeks->map(fn ($w) => (int) ($inProcessData->firstWhere('week', $w)?->count ?? 0))->values()->all();

        // Human-readable labels: "Week 23 Jun"
        $labels = $weeks->map(function ($w) {
            [$year, $week] = explode('-', $w);
            $date = Carbon::now()->setISODate((int) $year, (int) $week)->startOfWeek();
            return 'W' . $week . ' ' . $date->format('d M');
        })->all();

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Completed',
                    'data'            => $completedCounts,
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                    'tension'         => 0.4,
                    'fill'            => true,
                ],
                [
                    'label'           => 'In Process',
                    'data'            => $inProcessCounts,
                    'borderColor'     => 'rgb(251, 191, 36)',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.10)',
                    'tension'         => 0.4,
                    'fill'            => true,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1],
                    'grid'        => ['color' => 'rgba(156,163,175,0.15)'],
                ],
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
