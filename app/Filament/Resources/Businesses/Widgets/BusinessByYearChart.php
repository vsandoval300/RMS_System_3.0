<?php

namespace App\Filament\Resources\Businesses\Widgets;

use App\Enums\BusinessLifecycleStatus;
use App\Models\Business;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\Reactive;

class BusinessByYearChart extends ChartWidget
{
    #[Reactive]
    public ?array $tableFilters = null;

    #[Reactive]
    public ?string $tableSearch = null;

    protected ?string $heading = 'Businesses by Year and Lifecycle Status';
    protected ?string $maxHeight = '320px';
    protected string $view = 'filament.widgets.business-by-year-chart';
    protected int|string|array $columnSpan = 'full';

    private array $statusColors = [
        'On Hold'   => ['rgba(255,241,208,0.85)', '#FFF1D0'],  // Papaya Whip    – On Hold
        'In Force'  => ['rgba(6,174,213,0.85)',   '#06AED5'],  // Turquoise Surf – In Force
        'To Expire' => ['rgba(240,200,8,0.85)',   '#F0C808'],  // Bright Amber   – To Expire
        'Expired'   => ['rgba(8,103,136,0.85)',   '#086788'],  // Cerulean       – Expired
        'Cancelled' => ['rgba(221,28,26,0.85)',   '#DD1C1A'],  // Racing Red     – Cancelled
    ];

    protected function getData(): array
    {
        $filters = $this->tableFilters ?? [];

        $reinsurerId = data_get($filters, 'reinsurer_id.value');
        $interval = data_get($filters, 'date_interval.interval');
        $from = data_get($filters, 'date_interval.from');
        $until = data_get($filters, 'date_interval.until');

        $applyFilters = function ($query) use ($reinsurerId, $interval, $from, $until) {
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

            return $query;
        };

        $years = $applyFilters(Business::query()->searchGlobally($this->tableSearch))
            ->selectRaw("LEFT(business_code, 4) as yr")
            ->groupByRaw("LEFT(business_code, 4)")
            ->orderByRaw("LEFT(business_code, 4)")
            ->pluck('yr');

        $counts = $applyFilters(Business::query()->searchGlobally($this->tableSearch))
            ->selectRaw("LEFT(business_code, 4) as yr, business_lifecycle_status, COUNT(*) as total")
            ->groupByRaw("LEFT(business_code, 4), business_lifecycle_status")
            ->get()
            ->groupBy('yr');

        $datasets = [];
        $statuses  = BusinessLifecycleStatus::cases();

        foreach ($statuses as $index => $status) {
            $data = [];
            foreach ($years as $year) {
                $row = ($counts->get($year) ?? collect())
                    ->first(fn ($r) => $r->business_lifecycle_status === $status->value
                        || (is_object($r->business_lifecycle_status) && $r->business_lifecycle_status === $status));
                $data[] = $row ? (int) $row->total : 0;
            }

            $color = $this->statusColors[$status->value]
                ?? ['rgba(65,162,195,0.85)', 'rgba(65,162,195,1)'];

            $dataset = [
                'label'           => $status->value,
                'data'            => $data,
                'backgroundColor' => $color[0],
                'borderColor'     => $color[1],
                'borderWidth'     => 1,
                'borderRadius'    => 0,
            ];

            if ($index === 0) {
                $dataset['chartId'] = 'businesses-per-year';
            }

            $datasets[] = $dataset;
        }

        return [
            'labels'   => $years->map(fn ($y) => (string) $y)->all(),
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
            'layout' => [
                'padding' => ['top' => 24],
            ],
            'scales' => [
                'y' => [
                    'stacked'     => true,
                    'beginAtZero' => true,
                    'ticks'       => [
                        'stepSize' => 1,
                        'color'    => 'rgba(255,255,255,0.75)',
                        'font'     => ['size' => 12],
                    ],
                    'grid'        => ['color' => 'rgba(156,163,175,0.15)'],
                ],
                'x' => [
                    'stacked' => true,
                    'ticks'   => [
                        'color' => 'rgba(255,255,255,0.75)',
                        'font'  => ['size' => 12],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'bottom',
                    'labels'   => [
                        'color'     => 'rgba(255,255,255,0.75)',
                        'font'      => ['size' => 13],
                        'boxWidth'  => 12,
                        'boxHeight' => 12,
                        'padding'   => 16,
                    ],
                ],
            ],
        ];
    }
}
