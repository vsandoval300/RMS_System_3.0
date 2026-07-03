<?php

namespace App\Filament\Underwritten\Widgets;

use App\Services\PremiumForPeriodService;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class AnalyticsPremiumChart extends Widget
{
    protected string $view = 'filament.widgets.analytics-premium-chart';
    protected int|string|array $columnSpan = 1;
    protected static bool $isLazy = false;

    public int $year;
    public ?int $reinsurer = null;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    #[On('analytics-filters-updated')]
    public function updateFromAnalyticsFilters(int $year, ?int $reinsurer): void
    {
        $this->year      = $year;
        $this->reinsurer = $reinsurer;
    }

    public function getChartData(): array
    {
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        $svc  = app(PremiumForPeriodService::class);
        $data = $svc->monthlyFTS($this->reinsurer, [$this->year, $this->year - 1]);

        $datasets  = collect($data['datasets'] ?? []);
        $acDataset = $datasets->firstWhere('label', (string) $this->year);
        $plDataset = $datasets->firstWhere('label', (string) ($this->year - 1));

        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $acVal = (float) ($acDataset['data'][$i] ?? 0);
            $plVal = (float) ($plDataset['data'][$i] ?? 0);

            if ($plVal > 0) {
                $deltaPct = (int) round(($acVal - $plVal) / $plVal * 100);
            } else {
                $deltaPct = $acVal > 0 ? 100 : 0;
            }

            $rows[] = [
                'month'     => $months[$i],
                'ac'        => $acVal,
                'pl'        => $plVal,
                'delta_pct' => $deltaPct,
            ];
        }

        return $rows;
    }
}
