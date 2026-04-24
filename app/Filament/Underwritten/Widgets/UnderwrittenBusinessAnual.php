<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Models\Reinsurer;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UnderwrittenBusinessAnual extends ChartWidget
{
    protected static ?string $heading = 'Businesses per year';

    public ?int $reinsurer = null;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $query = Business::query()
            ->withoutGlobalScopes()
            ->from('businesses as b')
            ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code');

        // 👇 si el filtro NO es "all", filtramos por reinsurer_id
        if ($this->reinsurer) {
            $query->where('reinsurer_id', $this->reinsurer);
        }
        $rows = $query
            ->selectRaw("DATE_PART('year', od.rep_date) AS year, COUNT(*) AS total")
            ->where('od.operative_doc_type_id', '1') 
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Businesses',
                    'data'  => $rows->pluck('total'),
                    'fill' => false,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $rows->pluck('year'),
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
}
