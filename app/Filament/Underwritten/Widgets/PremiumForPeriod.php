<?php

namespace App\Filament\Underwritten\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\PremiumForPeriodService;

class PremiumForPeriod extends ChartWidget
{
    protected string $view = 'filament.widgets.premium-for-period';
    protected ?string $heading = 'Underwritten Premium';
    protected ?string $maxHeight = '300px';

    public ?int $reinsurer = null;
    protected static bool $isLazy = false;

    protected function getData(): array
    {

        $data = app(PremiumForPeriodService::class)->anualFTS($this->reinsurer);

        return [
            'datasets' => [
            [
                'label' => 'Premium.',
                'data' => $data['fts'],
                'fill' => false,
                'tension' => 0.3,
                'pointRadius' => 3,
            ],
        ],
        'labels' => $data['labels'],
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
    
    protected function hasFooter(): bool
    {
        return true;
    }
}
