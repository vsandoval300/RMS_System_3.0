<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Models\OperativeDoc;
use App\Models\Reinsurer;
use App\Models\UnderwrittenBudget;
use App\Services\PremiumForPeriodService;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BudgetPlanComparison extends Widget
{
    protected string $view = 'filament.widgets.budget-plan-comparison';

    public int    $selectedYear;
    public ?int   $selectedReinsurer = null;
    public bool    $showPlan         = false;
    public ?string $selectedBudgetId = null;
    public string $sortColumn        = 'code';
    public string $chartView         = 'table';

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public function updatedSelectedYear(): void
    {
        $this->selectedBudgetId = null;
        if ($this->showPlan) {
            $this->autoSelectBudget();
        }
        $this->dispatchBudgetFilters();
    }

    public function updatedSelectedReinsurer(): void
    {
        $this->dispatchBudgetFilters();
    }

    public function toggleShowPlan(): void
    {
        $this->showPlan = ! $this->showPlan;
        if ($this->showPlan && $this->selectedBudgetId === null) {
            $this->autoSelectBudget();
        }
        $this->dispatchBudgetFilters();
    }

    public function updatedShowPlan(): void
    {
        if ($this->showPlan && $this->selectedBudgetId === null) {
            $this->autoSelectBudget();
        }
        $this->dispatchBudgetFilters();
    }

    public function updatedSelectedBudgetId(): void
    {
        $this->dispatchBudgetFilters();
    }

    private function dispatchBudgetFilters(): void
    {
        $this->dispatch('budget-plan-filters-updated',
            year:      $this->selectedYear,
            reinsurer: $this->selectedReinsurer,
            showPlan:  $this->showPlan,
            budgetId:  $this->selectedBudgetId,
        );
    }

    private function autoSelectBudget(): void
    {
        $first = UnderwrittenBudget::where('year', $this->selectedYear)
            ->orderBy('version')
            ->first();
        $this->selectedBudgetId = $first?->id;
    }

    public function setSortColumn(string $column): void
    {
        $this->sortColumn = $column;
    }

    public function getAvailableBudgets(): array
    {
        return UnderwrittenBudget::where('year', $this->selectedYear)
            ->orderBy('version')
            ->get()
            ->mapWithKeys(fn ($b) => [$b->id => "v{$b->version} — {$b->label}"])
            ->toArray();
    }

    public function getReinsurers(): array
    {
        $ids = Business::withoutGlobalScopes()
            ->whereNotNull('reinsurer_id')
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('reinsurer_id');

        return Reinsurer::whereIn('id', $ids)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getAvailableYears(): array
    {
        return DB::table('operative_docs')
            ->selectRaw('EXTRACT(YEAR FROM rep_date)::int as year')
            ->whereNotNull('rep_date')
            ->groupByRaw('EXTRACT(YEAR FROM rep_date)')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
    }

    public function getBusinessCounts(): array
    {
        $year     = $this->selectedYear;
        $prevYear = $year - 1;

        $counts = DB::table('operative_docs as d')
            ->when($this->selectedReinsurer, fn ($q) =>
                $q->join('businesses as b', 'b.business_code', '=', 'd.business_code')
                  ->where('b.reinsurer_id', $this->selectedReinsurer)
            )
            ->selectRaw('EXTRACT(YEAR FROM d.rep_date)::int as year, COUNT(DISTINCT d.business_code) as cnt')
            ->whereRaw('EXTRACT(YEAR FROM d.rep_date) IN (?, ?)', [$year, $prevYear])
            ->groupByRaw('EXTRACT(YEAR FROM d.rep_date)')
            ->pluck('cnt', 'year');

        return [
            'ac' => (int) ($counts[$year]     ?? 0),
            'pl' => (int) ($counts[$prevYear] ?? 0),
        ];
    }

    public function getData(): array
    {
        $year     = $this->selectedYear;
        $prevYear = $year - 1;

        $docs = OperativeDoc::query()
            ->with(['business.reinsurer', 'schemes.costScheme', 'insureds'])
            ->where(fn ($q) => $q
                ->whereYear('rep_date', $year)
                ->orWhereYear('rep_date', $prevYear)
            )
            ->when($this->selectedReinsurer, fn ($q) =>
                $q->whereHas('business', fn ($b) =>
                    $b->where('reinsurer_id', $this->selectedReinsurer)
                )
            )
            ->get();

        $byReinsurer = [];

        foreach ($docs as $doc) {
            $docYear      = Carbon::parse($doc->rep_date)->year;
            $reinsurer    = $doc->business?->reinsurer;
            $reinsurerId  = $reinsurer?->id ?? 0;
            $name         = $reinsurer?->short_name ?? 'Unknown';
            $cnsCode      = $reinsurer?->cns_reinsurer ?? $reinsurer?->id ?? '';

            $inception    = Carbon::parse($doc->inception_date);
            $expiration   = Carbon::parse($doc->expiration_date);
            $daysInYear   = $inception->isLeapYear() ? 366 : 365;
            $coverageDays = $inception->diffInDays($expiration);

            $fts = 0;
            foreach ($doc->insureds as $insured) {
                $share = optional(
                    $doc->schemes->firstWhere('costScheme.id', $insured->cscheme_id)
                )->costScheme->share ?? 0;

                $ftpIndividual = $daysInYear > 0
                    ? ($insured->premium / $daysInYear) * $coverageDays
                    : 0;

                $fts += $ftpIndividual * $share;
            }

            $ftsConverted = $doc->roe_fs > 0 ? $fts / $doc->roe_fs : 0;

            if (! isset($byReinsurer[$reinsurerId])) {
                $byReinsurer[$reinsurerId] = [
                    'name'         => $name,
                    'cns_code'     => $cnsCode,
                    'reinsurer_id' => $reinsurerId,
                    'ac'           => 0.0,
                    'pl'           => 0.0,
                ];
            }

            if ($docYear === $year) {
                $byReinsurer[$reinsurerId]['ac'] += $ftsConverted;
            } else {
                $byReinsurer[$reinsurerId]['pl'] += $ftsConverted;
            }
        }

        // Load budget / plan data
        $planByReinsurerId = [];
        if ($this->showPlan && $this->selectedBudgetId) {
            $budget = UnderwrittenBudget::with('items')->find($this->selectedBudgetId);
            if ($budget) {
                foreach ($budget->items as $item) {
                    $planByReinsurerId[$item->reinsurer_id] = (float) $item->premium_budget;
                }
            }
        }

        if (empty($byReinsurer) && empty($planByReinsurerId)) {
            return [];
        }

        // Include budget-only reinsurers (in plan but no actual docs)
        if ($this->showPlan) {
            $reinsurers = Reinsurer::whereIn('id', array_keys($planByReinsurerId))->pluck('short_name', 'id');
            foreach (array_keys($planByReinsurerId) as $rid) {
                if (! isset($byReinsurer[$rid])) {
                    $byReinsurer[$rid] = [
                        'name'         => $reinsurers[$rid] ?? "ID:{$rid}",
                        'cns_code'     => $rid,
                        'reinsurer_id' => $rid,
                        'ac'           => 0.0,
                        'pl'           => 0.0,
                    ];
                }
            }
        }

        // Sort
        if ($this->sortColumn === 'ac') {
            uasort($byReinsurer, fn ($a, $b) => $b['ac'] <=> $a['ac']);
        } else {
            uasort($byReinsurer, fn ($a, $b) => strnatcasecmp((string) $a['cns_code'], (string) $b['cns_code']));
        }

        $deltas      = array_values(array_map(fn ($r) => abs($r['ac'] - $r['pl']), $byReinsurer));
        $maxAbsDelta = max(1, ...$deltas);

        $deltasPlan      = $this->showPlan
            ? array_values(array_map(fn ($r) => abs($r['ac'] - ($planByReinsurerId[$r['reinsurer_id']] ?? 0)), $byReinsurer))
            : [1];
        $maxAbsDeltaPlan = max(1, ...$deltasPlan);

        return array_values(array_map(function ($r) use ($maxAbsDelta, $maxAbsDeltaPlan, $planByReinsurerId) {
            $plan       = $planByReinsurerId[$r['reinsurer_id']] ?? 0;
            $deltaPlan  = $r['ac'] - $plan;

            return [
                'name'           => $r['name'],
                'cns_code'       => $r['cns_code'],
                'reinsurer_id'   => $r['reinsurer_id'],
                'ac'             => $r['ac'],
                'pl'             => $r['pl'],
                'delta'          => $r['ac'] - $r['pl'],
                'bar_pct'        => round(abs($r['ac'] - $r['pl']) / $maxAbsDelta * 100, 1),
                'delta_pct'      => $r['pl'] > 0
                    ? round(($r['ac'] - $r['pl']) / $r['pl'] * 100, 1)
                    : ($r['ac'] > 0 ? 100.0 : 0.0),
                'plan'           => $plan,
                'delta_plan'     => $deltaPlan,
                'bar_pct_plan'   => round(abs($deltaPlan) / $maxAbsDeltaPlan * 100, 1),
                'delta_pct_plan' => $plan > 0
                    ? round($deltaPlan / $plan * 100, 1)
                    : ($r['ac'] > 0 ? 100.0 : 0.0),
            ];
        }, $byReinsurer));
    }

    // ── Monthly Performance charts ─────────────────────────

    public function getBusinessChartData(): array
    {
        $months   = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $year     = $this->selectedYear;
        $prevYear = $year - 1;

        $fetch = function (int $y) {
            $q = Business::withoutGlobalScopes()
                ->from('businesses as b')
                ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code')
                ->selectRaw('EXTRACT(MONTH FROM od.rep_date) as month, COUNT(DISTINCT od.business_code) as total')
                ->where('od.operative_doc_type_id', '1')
                ->whereNull('b.deleted_at')
                ->whereRaw('EXTRACT(YEAR FROM od.rep_date) = ?', [$y])
                ->groupByRaw('EXTRACT(MONTH FROM od.rep_date)');

            if ($this->selectedReinsurer) {
                $q->where('b.reinsurer_id', $this->selectedReinsurer);
            }

            return $q->get()->keyBy(fn ($r) => (int) $r->month);
        };

        $ac = $fetch($year);
        $py = $fetch($prevYear);

        $rows = [];
        for ($m = 1; $m <= 12; $m++) {
            $acVal    = (int) ($ac[$m]->total ?? 0);
            $pyVal    = (int) ($py[$m]->total ?? 0);
            $deltaPct = $pyVal > 0
                ? (int) round(($acVal - $pyVal) / $pyVal * 100)
                : ($acVal > 0 ? 100 : 0);

            $rows[] = ['month' => $months[$m - 1], 'ac' => $acVal, 'delta_pct' => $deltaPct];
        }

        return $rows;
    }

    public function getPremiumChartDataPY(): array
    {
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $year   = $this->selectedYear;

        $svc       = app(PremiumForPeriodService::class);
        $acData    = $svc->monthlyFTS($this->selectedReinsurer, [$year]);
        $acDataset = collect($acData['datasets'] ?? [])->firstWhere('label', (string) $year);
        $pyData    = $svc->monthlyFTS($this->selectedReinsurer, [$year - 1]);
        $pyDataset = collect($pyData['datasets'] ?? [])->firstWhere('label', (string) ($year - 1));
        $pyMonthly = array_map(fn ($i) => (float) ($pyDataset['data'][$i] ?? 0), range(0, 11));

        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $acVal    = (float) ($acDataset['data'][$i] ?? 0);
            $compVal  = $pyMonthly[$i];
            $deltaPct = $compVal > 0
                ? (int) round(($acVal - $compVal) / $compVal * 100)
                : ($acVal > 0 ? 100 : 0);
            $rows[] = ['month' => $months[$i], 'ac' => $acVal, 'delta_pct' => $deltaPct];
        }

        return $rows;
    }

    public function getPremiumChartDataPL(): array
    {
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $year   = $this->selectedYear;

        $svc       = app(PremiumForPeriodService::class);
        $acData    = $svc->monthlyFTS($this->selectedReinsurer, [$year]);
        $acDataset = collect($acData['datasets'] ?? [])->firstWhere('label', (string) $year);
        $plMonthly = $this->selectedBudgetId ? $this->getBudgetMonthlyByColumn() : array_fill(0, 12, 0.0);

        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $acVal    = (float) ($acDataset['data'][$i] ?? 0);
            $compVal  = $plMonthly[$i];
            $deltaPct = $compVal > 0
                ? (int) round(($acVal - $compVal) / $compVal * 100)
                : ($acVal > 0 ? 100 : 0);
            $rows[] = ['month' => $months[$i], 'ac' => $acVal, 'delta_pct' => $deltaPct];
        }

        return $rows;
    }

    private function getBudgetMonthlyByColumn(): array
    {
        $budget = UnderwrittenBudget::with('items')->find($this->selectedBudgetId);

        if (! $budget) {
            return array_fill(0, 12, 0.0);
        }

        $cols    = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];
        $items   = $this->selectedReinsurer
            ? $budget->items->where('reinsurer_id', $this->selectedReinsurer)
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
