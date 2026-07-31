<?php

namespace App\Filament\Underwritten\Widgets;

use App\Services\PremiumForPeriodService;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class GwpPremiumChart extends Widget
{
    protected string $view = 'filament.widgets.gwp-premium-chart';
    protected int|string|array $columnSpan = 1;
    protected static bool $isLazy = false;

    public int  $year;
    public ?int $reinsurer = null;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    #[On('gwp-filters-updated')]
    public function updateFromGwpFilters(int $year, ?int $reinsurer): void
    {
        $this->year      = $year;
        $this->reinsurer = $reinsurer;
    }

    public function getChartData(): array
    {
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        $svc  = app(PremiumForPeriodService::class);
        $data = $svc->monthlyFTS($this->reinsurer, [$this->year]);

        $datasets  = collect($data['datasets'] ?? []);
        $acDataset = $datasets->firstWhere('label', (string) $this->year);

        $pyData    = $svc->monthlyFTS($this->reinsurer, [$this->year - 1]);
        $pyDataset = collect($pyData['datasets'] ?? [])->firstWhere('label', (string) ($this->year - 1));
        $plMonthly = array_map(fn ($i) => (float) ($pyDataset['data'][$i] ?? 0), range(0, 11));

        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $acVal = (float) ($acDataset['data'][$i] ?? 0);
            $plVal = (float) $plMonthly[$i];

            $deltaPct = $plVal > 0
                ? (int) round(($acVal - $plVal) / $plVal * 100)
                : ($acVal > 0 ? 100 : 0);

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
