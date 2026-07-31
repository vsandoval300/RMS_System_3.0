<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\UnderwrittenBudget;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class AnalyticsPremiumChart extends Widget
{
    protected string $view = 'filament.widgets.analytics-premium-chart';
    protected int|string|array $columnSpan = 1;
    protected static bool $isLazy = false;

    public int     $year;
    public ?int    $reinsurer = null;
    public bool    $showPlan  = false;
    public ?string $budgetId  = null;

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

    #[On('budget-plan-filters-updated')]
    public function updateFromBudgetFilters(int $year, ?int $reinsurer, bool $showPlan, ?string $budgetId): void
    {
        $this->year      = $year;
        $this->reinsurer = $reinsurer;
        $this->showPlan  = $showPlan;
        $this->budgetId  = $budgetId;
    }

    public function getChartData(): array
    {
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // AC series: always from actual operative docs
        $svc  = app(PremiumForPeriodService::class);
        $data = $svc->monthlyFTS($this->reinsurer, [$this->year]);

        $datasets  = collect($data['datasets'] ?? []);
        $acDataset = $datasets->firstWhere('label', (string) $this->year);

        // Comparison series: budget plan (m01–m12) or prior year
        if ($this->showPlan && $this->budgetId) {
            $plMonthly = $this->getBudgetMonthlyData();
        } else {
            $pyData    = $svc->monthlyFTS($this->reinsurer, [$this->year - 1]);
            $pyDataset = collect($pyData['datasets'] ?? [])->firstWhere('label', (string) ($this->year - 1));
            $plMonthly = array_map(fn ($i) => (float) ($pyDataset['data'][$i] ?? 0), range(0, 11));
        }

        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $acVal = (float) ($acDataset['data'][$i] ?? 0);
            $plVal = (float) $plMonthly[$i];

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

    private function getBudgetMonthlyData(): array
    {
        $budget = UnderwrittenBudget::with('items')->find($this->budgetId);

        if (! $budget) {
            return array_fill(0, 12, 0.0);
        }

        $cols = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];

        $items = $this->reinsurer
            ? $budget->items->where('reinsurer_id', $this->reinsurer)
            : $budget->items;

        $monthly = array_fill(0, 12, 0.0);
        foreach ($items as $item) {
            foreach ($cols as $i => $col) {
                $monthly[$i] += (float) $item->$col;
            }
        }

        return $monthly;
    }
}
