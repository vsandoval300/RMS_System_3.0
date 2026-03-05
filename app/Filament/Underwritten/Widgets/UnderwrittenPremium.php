<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Log;

class UnderwrittenPremium extends ChartWidget
{
    protected static ?string $heading = 'Underwritten Premium';

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
        $data = app(PremiumForPeriodService::class)
            ->monthlyFTSByYear($this->reinsurer, $this->years);
         Log::info('Datos para chart', $data);
        return [
            'datasets' => $data['datasets'],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}