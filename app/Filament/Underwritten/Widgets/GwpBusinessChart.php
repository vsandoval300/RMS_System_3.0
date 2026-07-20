<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class GwpBusinessChart extends Widget
{
    protected string $view = 'filament.widgets.gwp-business-chart';
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

        $fetch = function (int $year): \Illuminate\Support\Collection {
            $q = Business::withoutGlobalScopes()
                ->from('businesses as b')
                ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code')
                ->selectRaw('EXTRACT(MONTH FROM od.rep_date) as month, COUNT(DISTINCT od.business_code) as total')
                ->where('od.operative_doc_type_id', '1')
                ->whereNull('b.deleted_at')
                ->where('b.approval_status', 'APR')
                ->whereRaw('EXTRACT(YEAR FROM od.rep_date) = ?', [$year])
                ->groupByRaw('EXTRACT(MONTH FROM od.rep_date)');

            if ($this->reinsurer) {
                $q->where('b.reinsurer_id', $this->reinsurer);
            }

            return $q->get()->keyBy(fn ($r) => (int) $r->month);
        };

        $ac = $fetch($this->year);
        $pl = $fetch($this->year - 1);

        $rows = [];
        for ($m = 1; $m <= 12; $m++) {
            $acVal = (int) ($ac[$m]->total ?? 0);
            $plVal = (int) ($pl[$m]->total ?? 0);

            $deltaPct = $plVal > 0
                ? (int) round(($acVal - $plVal) / $plVal * 100)
                : ($acVal > 0 ? 100 : 0);

            $rows[] = [
                'month'     => $months[$m - 1],
                'ac'        => $acVal,
                'pl'        => $plVal,
                'delta_pct' => $deltaPct,
            ];
        }

        return $rows;
    }
}
