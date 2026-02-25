<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use App\Models\Reinsurer;
use Filament\Widgets\ChartWidget;

class UnderwrittenBusiness extends ChartWidget
{
    protected static ?string $heading = 'Businesses per year';

    public ?int $reinsurer = null;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $query = Business::query();

        // 👇 si el filtro NO es "all", filtramos por reinsurer_id
        if ($this->reinsurer) {
            $query->where('reinsurer_id', $this->reinsurer);
        }

        $rows = $query
            ->selectRaw("DATE_PART('year', created_at) AS year, COUNT(*) AS total")
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Businesses',
                    'data'  => $rows->pluck('total'),
                ],
            ],
            'labels' => $rows->pluck('year'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
