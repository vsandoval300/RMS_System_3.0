<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\OperativeDoc;
use App\Models\Reinsurer;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class ReinsurerSegmentation extends Widget
{
    protected string $view = 'filament.widgets.reinsurer-segmentation';
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public function getAvailableYears(): array
    {
        return DB::table('operative_docs')
            ->selectRaw("EXTRACT(YEAR FROM rep_date)::int as yr")
            ->whereNotNull('rep_date')
            ->groupByRaw("EXTRACT(YEAR FROM rep_date)")
            ->orderByRaw("EXTRACT(YEAR FROM rep_date) DESC")
            ->pluck('yr')
            ->toArray();
    }

    private function getReinsurerPoints(): array
    {
        $year = $this->selectedYear;

        $bizCounts = DB::table('businesses as b')
            ->join('operative_docs as od', 'od.business_code', '=', 'b.business_code')
            ->where('od.operative_doc_type_id', '1')
            ->whereNull('b.deleted_at')
            ->where('b.approval_status', 'APR')
            ->whereRaw("EXTRACT(YEAR FROM od.rep_date) = ?", [$year])
            ->whereNotNull('b.reinsurer_id')
            ->selectRaw('b.reinsurer_id, COUNT(DISTINCT od.business_code) as biz_count')
            ->groupBy('b.reinsurer_id')
            ->pluck('biz_count', 'reinsurer_id');

        $docs = OperativeDoc::query()
            ->with(['business.reinsurer', 'schemes.costScheme', 'insureds'])
            ->whereYear('rep_date', $year)
            ->whereHas('business', fn ($q) => $q->whereNotNull('reinsurer_id')->whereNull('deleted_at')->where('approval_status', 'APR'))
            ->get();

        $premByReinsurer = [];
        foreach ($docs as $doc) {
            $reinsurer = $doc->business?->reinsurer;
            if (! $reinsurer) continue;
            $inception    = Carbon::parse($doc->inception_date);
            $expiration   = Carbon::parse($doc->expiration_date);
            $daysInYear   = $inception->isLeapYear() ? 366 : 365;
            $coverageDays = $inception->diffInDays($expiration);
            $fts = 0.0;
            foreach ($doc->insureds as $insured) {
                $share = optional(
                    $doc->schemes->firstWhere('costScheme.id', $insured->cscheme_id)
                )->costScheme->share ?? 0;
                $fts += ($daysInYear > 0 ? ($insured->premium / $daysInYear) * $coverageDays : 0) * $share;
            }
            $ftsConverted = $doc->roe_fs > 0 ? $fts / $doc->roe_fs : 0.0;
            $id = $reinsurer->id;
            $premByReinsurer[$id] = ($premByReinsurer[$id] ?? 0.0) + $ftsConverted;
        }

        $allIds     = array_unique(array_merge($bizCounts->keys()->toArray(), array_keys($premByReinsurer)));
        $reinsurers = Reinsurer::whereIn('id', $allIds)->get()->keyBy('id');

        $points = [];
        foreach ($allIds as $id) {
            $r    = $reinsurers[$id] ?? null;
            if (! $r) continue;
            $biz  = (int)   ($bizCounts[$id]      ?? 0);
            $prem = (float) ($premByReinsurer[$id] ?? 0.0);
            if ($biz === 0 && $prem == 0.0) continue;
            $points[] = ['id' => $id, 'name' => $r->short_name ?? $r->name, 'biz' => $biz, 'premium' => $prem];
        }

        return $points;
    }

    public function getDendrogramData(): array
    {
        $points = $this->getReinsurerPoints();
        $n      = count($points);
        $year   = $this->selectedYear;

        if ($n < 2) {
            return ['leaves' => $points, 'merges' => [], 'clusterOf' => [], 'cutHeight' => 0, 'maxHeight' => 0, 'k' => $n, 'n' => $n, 'year' => $year];
        }

        // Z-score normalisation
        $xs = array_column($points, 'biz');
        $ys = array_column($points, 'premium');
        $mx = array_sum($xs) / $n;
        $my = array_sum($ys) / $n;
        $sx = sqrt(array_sum(array_map(fn ($x) => ($x - $mx) ** 2, $xs)) / max($n - 1, 1)) ?: 1.0;
        $sy = sqrt(array_sum(array_map(fn ($y) => ($y - $my) ** 2, $ys)) / max($n - 1, 1)) ?: 1.0;
        $norm = array_map(fn ($p) => [($p['biz'] - $mx) / $sx, ($p['premium'] - $my) / $sy], $points);

        // Euclidean distance matrix
        $dist = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $dist[$i][$j] = sqrt(($norm[$i][0] - $norm[$j][0]) ** 2 + ($norm[$i][1] - $norm[$j][1]) ** 2);
            }
        }

        // Agglomerative clustering — complete linkage
        $nodeMembers = [];
        for ($i = 0; $i < $n; $i++) $nodeMembers[$i] = [$i];
        $nextId = $n;
        $merges = [];
        $active = range(0, $n - 1);

        while (count($active) > 1) {
            $minDist = INF; $minI = $minJ = -1;
            for ($ai = 0; $ai < count($active); $ai++) {
                for ($aj = $ai + 1; $aj < count($active); $aj++) {
                    $ci = $active[$ai]; $cj = $active[$aj]; $d = 0;
                    foreach ($nodeMembers[$ci] as $li) {
                        foreach ($nodeMembers[$cj] as $lj) {
                            $d = max($d, $dist[$li][$lj]);
                        }
                    }
                    if ($d < $minDist) { $minDist = $d; $minI = $ci; $minJ = $cj; }
                }
            }
            $newId = $nextId++;
            $nodeMembers[$newId] = array_merge($nodeMembers[$minI], $nodeMembers[$minJ]);
            $merges[] = ['left' => $minI, 'right' => $minJ, 'height' => round($minDist, 5), 'id' => $newId];
            $active   = array_values(array_filter($active, fn ($c) => $c !== $minI && $c !== $minJ));
            $active[] = $newId;
        }

        // Determine cut height → k = 3 clusters
        $k           = min(3, $n);
        $activeTrack = range(0, $n - 1);
        $clusterRoots = $activeTrack;
        $cutHeight    = 0.0;

        foreach ($merges as $idx => $m) {
            $activeTrack = array_values(array_filter($activeTrack, fn ($c) => $c !== $m['left'] && $c !== $m['right']));
            $activeTrack[] = $m['id'];
            if (count($activeTrack) === $k) {
                $clusterRoots = $activeTrack;
                $prevH     = $idx > 0 ? $merges[$idx - 1]['height'] : 0.0;
                $cutHeight = round($prevH + ($m['height'] - $prevH) * 0.5, 5);
                break;
            }
        }

        // Assign cluster to each leaf
        $getLeavesOf = null;
        $getLeavesOf = function (int $nodeId) use ($n, $merges, &$getLeavesOf): array {
            if ($nodeId < $n) return [$nodeId];
            foreach ($merges as $m) {
                if ($m['id'] === $nodeId) {
                    return array_merge($getLeavesOf($m['left']), $getLeavesOf($m['right']));
                }
            }
            return [];
        };

        $clusterOf = array_fill(0, $n, 0);
        foreach ($clusterRoots as $ci => $rootId) {
            foreach ($getLeavesOf($rootId) as $leafIdx) {
                $clusterOf[$leafIdx] = $ci;
            }
        }

        $leaves    = array_map(fn ($p, $i) => array_merge($p, ['idx' => $i, 'cluster' => $clusterOf[$i]]), $points, array_keys($points));
        $maxHeight = $merges[count($merges) - 1]['height'];

        return [
            'leaves'    => $leaves,
            'merges'    => $merges,
            'clusterOf' => array_values($clusterOf),
            'cutHeight' => $cutHeight,
            'maxHeight' => $maxHeight,
            'k'         => $k,
            'n'         => $n,
            'year'      => $year,
        ];
    }
}
