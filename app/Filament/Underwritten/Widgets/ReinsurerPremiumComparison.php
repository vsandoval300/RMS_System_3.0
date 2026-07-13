<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Models\OperativeDoc;
use App\Models\Reinsurer;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class ReinsurerPremiumComparison extends Widget
{
    protected string $view = 'filament.widgets.reinsurer-premium-comparison';

    public int    $selectedYear;
    public ?int   $selectedReinsurer = null;
    public string $sortColumn        = 'code';
    public string $chartView         = 'table';

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public function updatedSelectedYear(): void
    {
        $this->dispatchFiltersUpdated();
    }

    public function updatedSelectedReinsurer(): void
    {
        $this->dispatchFiltersUpdated();
    }

    public function setSortColumn(string $column): void
    {
        $this->sortColumn = $column;
    }

    private function dispatchFiltersUpdated(): void
    {
        $this->dispatch('analytics-filters-updated',
            year:      $this->selectedYear,
            reinsurer: $this->selectedReinsurer,
        );
    }

    public function getReinsurers(): array
    {
        $ids = Business::withoutGlobalScopes()
            ->whereNotNull('reinsurer_id')
            ->whereNull('deleted_at')
            ->where('approval_status', 'APR')
            ->distinct()
            ->pluck('reinsurer_id');

        return Reinsurer::whereIn('id', $ids)
            ->orderBy('name')
            ->pluck('name', 'id')
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

    public function getData(): array
    {
        $year     = $this->selectedYear;
        $prevYear = $year - 1;

        $docs = OperativeDoc::query()
            ->with([
                'business.reinsurer',
                'schemes.costScheme',
                'insureds',
            ])
            ->where(fn ($q) => $q
                ->whereYear('rep_date', $year)
                ->orWhereYear('rep_date', $prevYear)
            )
            ->whereHas('business', fn ($b) => $b->where('approval_status', 'APR'))
            ->when($this->selectedReinsurer, fn ($q) =>
                $q->whereHas('business', fn ($b) =>
                    $b->where('reinsurer_id', $this->selectedReinsurer)
                )
            )
            ->get();

        $byReinsurer = [];

        foreach ($docs as $doc) {
            $docYear     = Carbon::parse($doc->rep_date)->year;
            $reinsurer   = $doc->business?->reinsurer;
            $name        = $reinsurer?->short_name ?? 'Unknown';
            $cnsCode     = $reinsurer?->cns_reinsurer ?? $reinsurer?->id ?? '';

            $inception   = Carbon::parse($doc->inception_date);
            $expiration  = Carbon::parse($doc->expiration_date);
            $daysInYear  = $inception->isLeapYear() ? 366 : 365;
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

            if (! isset($byReinsurer[$name])) {
                $byReinsurer[$name] = ['ac' => 0.0, 'pl' => 0.0, 'cns_code' => $cnsCode];
            }

            if ($docYear === $year) {
                $byReinsurer[$name]['ac'] += $ftsConverted;
            } else {
                $byReinsurer[$name]['pl'] += $ftsConverted;
            }
        }

        if (empty($byReinsurer)) {
            return [];
        }

        if ($this->sortColumn === 'ac') {
            uasort($byReinsurer, fn ($a, $b) => $b['ac'] <=> $a['ac']);
        } else {
            uasort($byReinsurer, fn ($a, $b) => strnatcasecmp((string) $a['cns_code'], (string) $b['cns_code']));
        }

        $deltas = array_values(array_map(fn ($r) => abs($r['ac'] - $r['pl']), $byReinsurer));
        $maxAbsDelta = max(1, ...$deltas);

        return array_values(array_map(fn ($name, $r) => [
            'name'      => $name,
            'cns_code'  => $r['cns_code'],
            'ac'        => $r['ac'],
            'pl'        => $r['pl'],
            'delta'     => $r['ac'] - $r['pl'],
            'bar_pct'   => round(abs($r['ac'] - $r['pl']) / $maxAbsDelta * 100, 1),
            'delta_pct' => $r['pl'] > 0
                ? round(($r['ac'] - $r['pl']) / $r['pl'] * 100, 1)
                : ($r['ac'] > 0 ? 100.0 : 0.0),
        ], array_keys($byReinsurer), $byReinsurer));
    }
}
