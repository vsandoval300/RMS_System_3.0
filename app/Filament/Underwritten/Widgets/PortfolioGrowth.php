<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Models\Reinsurer;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class PortfolioGrowth extends Widget
{
    protected string $view = 'filament.widgets.portfolio-growth';
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    public ?int $reinsurer = null;

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

    public function getBusinessData(): array
    {
        $rows = Business::withoutGlobalScopes()
            ->from('businesses as b')
            ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code')
            ->selectRaw("DATE_PART('year', od.rep_date) AS year, COUNT(DISTINCT od.business_code) AS total")
            ->where('od.operative_doc_type_id', '1')
            ->whereNull('b.deleted_at')
            ->where('b.approval_status', 'APR')
            ->when($this->reinsurer, fn($q) => $q->where('b.reinsurer_id', $this->reinsurer))
            ->groupByRaw("DATE_PART('year', od.rep_date)")
            ->orderByRaw("DATE_PART('year', od.rep_date)")
            ->get();

        $result   = [];
        $prevTotal = null;

        foreach ($rows as $row) {
            $curr = (int) $row->total;

            if ($prevTotal !== null && $prevTotal > 0) {
                $deltaPct = (int) round(($curr - $prevTotal) / $prevTotal * 100);
            } else {
                $deltaPct = null;
            }

            $result[] = [
                'year'      => (int) $row->year,
                'total'     => $curr,
                'delta_pct' => $deltaPct,
            ];

            $prevTotal = $curr;
        }

        return $result;
    }

    public function getKpiData(): array
    {
        $premData = $this->getPremiumData();
        $bizData  = $this->getBusinessData();

        // ── CAGR: first non-zero year → penultimate year (avoids partial current year) ──
        $nonZero = array_values(array_filter($premData, fn ($r) => $r['premium'] > 0));
        $cagr    = null;

        if (count($nonZero) >= 2) {
            $first = $nonZero[0];
            $last  = $nonZero[count($nonZero) - 2];
            $years = $last['year'] - $first['year'];

            if ($years > 0 && $first['premium'] > 0) {
                $cagr = round((pow($last['premium'] / $first['premium'], 1 / $years) - 1) * 100, 1);
            }
        }

        // ── Cumulative premium ──
        $cumulative = array_sum(array_column($premData, 'premium'));

        // ── Record year (max premium) ──
        $recordRow = collect($premData)->sortByDesc('premium')->first();
        $recordBiz = 0;

        if ($recordRow) {
            foreach ($bizData as $brow) {
                if ($brow['year'] === $recordRow['year']) {
                    $recordBiz = $brow['total'];
                    break;
                }
            }
        }

        // ── Active reinsurers: current and previous year (global, ignores reinsurer filter) ──
        $currentYear = now()->year;
        $counts = DB::table('businesses as b')
            ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code')
            ->whereNull('b.deleted_at')
            ->where('b.approval_status', 'APR')
            ->where('od.operative_doc_type_id', '1')
            ->whereNotNull('b.reinsurer_id')
            ->whereRaw("DATE_PART('year', od.rep_date) IN (?, ?)", [$currentYear, $currentYear - 1])
            ->selectRaw("DATE_PART('year', od.rep_date) AS yr, COUNT(DISTINCT b.reinsurer_id) AS cnt")
            ->groupByRaw("DATE_PART('year', od.rep_date)")
            ->pluck('cnt', 'yr');

        $activeNow  = (int) ($counts[$currentYear] ?? 0);
        $activePrev = (int) ($counts[$currentYear - 1] ?? 0);

        // ── Sparklines ──
        $premSparkline = array_column($premData, 'premium');

        $running = 0;
        $cumSparkline = [];
        foreach ($premData as $row) {
            $running += $row['premium'];
            $cumSparkline[] = $running;
        }

        // Reinsurers per year sparkline (global)
        $reinsByYear = DB::table('businesses as b')
            ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code')
            ->whereNull('b.deleted_at')
            ->where('b.approval_status', 'APR')
            ->where('od.operative_doc_type_id', '1')
            ->whereNotNull('b.reinsurer_id')
            ->selectRaw("DATE_PART('year', od.rep_date) AS yr, COUNT(DISTINCT b.reinsurer_id) AS cnt")
            ->groupByRaw("DATE_PART('year', od.rep_date)")
            ->orderByRaw("DATE_PART('year', od.rep_date)")
            ->pluck('cnt', 'yr')
            ->values()
            ->map(fn ($v) => (int) $v)
            ->toArray();

        return [
            'cagr'           => $cagr,
            'cumulative'     => $cumulative,
            'record_year'    => $recordRow['year'] ?? null,
            'record_premium' => $recordRow['premium'] ?? 0,
            'record_biz'     => $recordBiz,
            'active_now'     => $activeNow,
            'active_prev'    => $activePrev,
            'active_year'    => $currentYear,
            'prem_sparkline' => $premSparkline,
            'cum_sparkline'  => $cumSparkline,
            'rein_sparkline' => $reinsByYear,
            'biz_sparkline'  => array_column($bizData, 'total'),
        ];
    }

    public function getPremiumData(): array
    {
        $data = app(PremiumForPeriodService::class)->anualFTS($this->reinsurer);

        $result  = [];
        $prevPrem = null;

        foreach ($data['labels'] as $i => $year) {
            $curr = (float) ($data['fts'][$i] ?? 0);

            if ($prevPrem !== null && $prevPrem > 0) {
                $deltaPct = (int) round(($curr - $prevPrem) / $prevPrem * 100);
            } else {
                $deltaPct = null;
            }

            $result[] = [
                'year'      => (int) $year,
                'premium'   => $curr,
                'delta_pct' => $deltaPct,
            ];

            $prevPrem = $curr;
        }

        return $result;
    }
}
