<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UnderwrittenBusiness extends ChartWidget
{
    protected static ?string $heading = 'Underwritten Business';

    public ?int $reinsurer = null;
    public array $years = [];

    protected static bool $isLazy = false;

    protected function getListeners(): array
    {
        return [
            'refreshChart' => '$refresh',
        ];
    }

    protected function getData(): array
    {
        if (empty($this->years)) {
            $this->years = [now()->year];
        }

        $months = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
            7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'
        ];

        $query = Business::query();

        if ($this->reinsurer) {
            $query->where('reinsurer_id', $this->reinsurer);
        }

        $query
            ->selectRaw("
                EXTRACT(YEAR FROM created_at) as year,
                EXTRACT(MONTH FROM created_at) as month,
                COUNT(*) as total
            ")
            ->whereIn(DB::raw("EXTRACT(YEAR FROM created_at)"), $this->years)
            ->groupByRaw('year, month')
            ->orderByRaw('year, month');

        $rows = $query->get();

        $grouped = [];

        foreach ($this->years as $year) {
            $grouped[$year] = array_fill(1,12,0);
        }

        foreach ($rows as $row) {

            $year = (int)$row->year;
            $month = (int)$row->month;

            $grouped[$year][$month] = (int)$row->total;
        }

        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#00CED1',
            '#8B0000'
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($grouped as $year => $monthsData) {

            $color = $colors[$colorIndex % count($colors)];

            $datasets[] = [
                'label' => (string)$year,
                'data' => array_values($monthsData),
                'borderColor' => $color,
                //'backgroundColor' => $color.'80',
                'fill' => false,
                'tension' => 0.3,
                'pointRadius' => 3,
            ];

            $colorIndex++;
        }

        return [
            'labels' => array_values($months),
            'datasets' => $datasets
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,

                    'grid' => [
                        'color' => 'rgba(156, 163, 175, 0.15)', // líneas horizontales suaves
                        'drawBorder' => false,
                    ],

                    'ticks' => [
                        'color' => '#9CA3AF',
                        'padding' => 8,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
