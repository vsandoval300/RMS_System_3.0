<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use Filament\Widgets\ChartWidget;

class UnderwrittenBusiness extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    public ?int $reinsurer = null;
    public ?int $year = null;

    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $query = Business::query();

        if($this->reinsurer) {
            $query->where('reinsurer_id', $this->reinsurer);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
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
