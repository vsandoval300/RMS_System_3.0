<?php

namespace App\Filament\Underwritten\Resources\NoResource\Widgets;

use Filament\Widgets\ChartWidget;
use  App\Models\Reinsurer;
use App\Models\Business;
use App\Services\PremiumForPeriodService;

class PremiumForPeriod extends ChartWidget
{
    protected static ?string $heading = 'Premium subscribed per period';

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        return [
            'all' => 'All reinsurers',
        ] + Reinsurer::query()
            ->orderBy('name')
            ->pluck('name', 'id')   // [id => name]
            ->toArray();
    }

    protected function getData(): array
    {
       
        $filter = $this->filter ?? 'all';

        $data = app(PremiumForPeriodService::class)->anualFTS($filter);


        return [
            'datasets' => [
            // [
            //     'label' => 'FTP',
            //     'data' => $data['ftp'],
            //     'borderColor' => '#3b82f6',
            //     'tension' => 0.4,
            // ],
            [
                'label' => 'FTS',
                'data' => $data['fts'],
                'borderColor' => '#10b981',
                'tension' => 0.4,
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
