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

        $query = Business::query()
            ->withoutGlobalScopes()
            ->from('businesses as b')
            ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code');

        if ($this->reinsurer) {
            $query->where('b.reinsurer_id', $this->reinsurer);
        }

        $query->selectRaw('
                EXTRACT(YEAR FROM od.rep_date) as year,
                EXTRACT(MONTH FROM od.rep_date) as month,
                COUNT(DISTINCT od.business_code) as total
            ')
            ->where('od.operative_doc_type_id', '1') 
            ->whereNull('b.deleted_at')
            ->whereIn(DB::raw('EXTRACT(YEAR FROM od.rep_date)'), $this->years)
            ->groupByRaw('
                EXTRACT(YEAR FROM od.rep_date),
                EXTRACT(MONTH FROM od.rep_date)
            ')
            ->orderBy('year')
            ->orderBy('month');

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
