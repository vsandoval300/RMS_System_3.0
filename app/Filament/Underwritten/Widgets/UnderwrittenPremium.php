<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\ChartWidget;

class UnderwrittenPremium extends ChartWidget
{
    protected static ?string $heading = 'Underwritten Premium';

    public ?int $reinsurer = null;
    public ?int $year = null;

    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $data = app(PremiumForPeriodService::class)->anualFTS($this->reinsurer, $this->year);

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