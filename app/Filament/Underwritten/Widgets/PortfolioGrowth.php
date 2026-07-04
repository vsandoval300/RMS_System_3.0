<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Models\Reinsurer;
use App\Services\PremiumForPeriodService;
use Filament\Widgets\Widget;

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
