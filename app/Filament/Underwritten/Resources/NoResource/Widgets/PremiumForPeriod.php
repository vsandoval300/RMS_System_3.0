<?php

namespace App\Filament\Underwritten\Resources\NoResource\Widgets;

use Filament\Widgets\ChartWidget;
use  App\Models\Reinsurer;
use App\Models\Business;
use App\Services\PremiumForPeriodService;
use Filament\Support\RawJs;

class PremiumForPeriod extends ChartWidget
{
    protected static ?string $heading = 'Underwritten Premium';

    public ?int $reinsurer = null;
    protected static bool $isLazy = false;

    /*protected function getFilters(): ?array
    {
        return [
            'all' => 'All reinsurers',
        ] + Reinsurer::query()
            ->orderBy('name')
            ->pluck('name', 'id')   // [id => name]
            ->toArray();
    }*/

    protected function getData(): array
    {
       
        //$filter = $this->filter ?? 'all';
        //dd($this->reinsurer);
        $data = app(PremiumForPeriodService::class)->anualFTS($this->reinsurer);


        return [
            'datasets' => [
            [
                'label' => 'FTS',
                'data' => $data['fts'],
            ],
        ],
        'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
