<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamPerformance extends Widget
{
    protected string $view            = 'filament.widgets.team-performance';
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy     = false;

    public string  $period   = 'month'; // month | year | quarter | custom | all
    public ?int    $quarter  = null;    // 1-4 when period === 'quarter'
    public ?string $dateFrom = null;    // YYYY-MM when period === 'custom'
    public ?string $dateTo   = null;    // YYYY-MM when period === 'custom'

    public function setQuarter(int $q): void
    {
        $this->period  = 'quarter';
        $this->quarter = $q;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $f = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1));
        $l = mb_strtoupper(mb_substr($parts[count($parts) - 1] ?? '', 0, 1));
        return $f . ($l !== $f ? $l : '');
    }

    private function allSubordinateIds(int $userId): array
    {
        $direct = User::where('manager_id', $userId)->pluck('id')->toArray();
        $all    = $direct;
        foreach ($direct as $id) {
            $all = array_merge($all, $this->allSubordinateIds($id));
        }
        return array_unique($all);
    }

    private function applyPeriod($query, string $table = 'businesses')
    {
        return match ($this->period) {
            'month'   => $query
                ->whereMonth("{$table}.created_at", now()->month)
                ->whereYear("{$table}.created_at", now()->year),
            'year'    => $query->whereYear("{$table}.created_at", now()->year),
            'quarter' => $this->applyQuarterFilter($query, $table, $this->quarter ?? now()->quarter, now()->year),
            'custom'  => $this->applyCustomFilter($query, $table),
            default   => $query,
        };
    }

    private function applyQuarterFilter($query, string $table, int $q, int $year)
    {
        $startMonth = ($q - 1) * 3 + 1;
        $endMonth   = $q * 3;
        return $query
            ->whereYear("{$table}.created_at", $year)
            ->whereMonth("{$table}.created_at", '>=', $startMonth)
            ->whereMonth("{$table}.created_at", '<=', $endMonth);
    }

    private function applyCustomFilter($query, string $table)
    {
        if ($this->dateFrom) {
            $query->where("{$table}.created_at", '>=',
                \Carbon\Carbon::parse($this->dateFrom . '-01')->startOfMonth());
        }
        if ($this->dateTo) {
            $query->where("{$table}.created_at", '<=',
                \Carbon\Carbon::parse($this->dateTo . '-01')->endOfMonth());
        }
        return $query;
    }

    private function hasPrevPeriod(): bool
    {
        return in_array($this->period, ['month', 'year', 'quarter']);
    }

    private function applyPrevPeriod($query, string $table = 'businesses')
    {
        if ($this->period === 'month') {
            $prev = now()->subMonth();
            return $query
                ->whereMonth("{$table}.created_at", $prev->month)
                ->whereYear("{$table}.created_at", $prev->year);
        }
        if ($this->period === 'year') {
            return $query->whereYear("{$table}.created_at", now()->subYear()->year);
        }
        if ($this->period === 'quarter') {
            $q    = $this->quarter ?? now()->quarter;
            $year = now()->year;
            $prevQ = $q === 1 ? 4 : $q - 1;
            $prevY = $q === 1 ? $year - 1 : $year;
            return $this->applyQuarterFilter($query, $table, $prevQ, $prevY);
        }
        return $query;
    }

    private function trendLabel(int $curr, int $prev): array
    {
        if ($prev === 0 && $curr === 0) return ['dir' => 'flat', 'pct' => 0];
        if ($prev === 0) return ['dir' => 'up', 'pct' => 100];
        $pct = round((($curr - $prev) / $prev) * 100);
        return ['dir' => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat'), 'pct' => abs($pct)];
    }

    // ── Tree builder (uses pre-loaded maps for efficiency) ────────────────────

    private function buildTree(int $managerId, array $statsMap, array $prevMap, array $activityMap, int $level = 1): array
    {
        $members = [];
        $users   = User::where('manager_id', $managerId)->orderBy('name')->get();

        foreach ($users as $user) {
            $s = $statsMap[$user->id] ?? null;

            $curr = (int) ($s->total    ?? 0);
            $prev = (int) ($prevMap[$user->id]->total ?? 0);
            $trend = $this->hasPrevPeriod() ? $this->trendLabel($curr, $prev) : null;

            $lastAct  = $activityMap[$user->id]->last_at ?? null;
            $daysSince = $lastAct ? (int) now()->diffInDays($lastAct) : null;

            $activityStatus = match (true) {
                $daysSince === null  => 'none',
                $daysSince <= 7      => 'green',
                $daysSince <= 30     => 'yellow',
                default              => 'red',
            };

            $total    = $curr;
            $approved = (int) ($s->approved ?? 0);
            $completion = $total > 0 ? round(($approved / $total) * 100) : 0;

            $members[] = [
                'user_id'         => $user->id,
                'name'            => $user->name,
                'initials'        => $this->initials($user->name),
                'level'           => $level,
                'activity_status' => $activityStatus,
                'days_since'      => $daysSince,
                'draft'           => (int) ($s->draft   ?? 0),
                'pending'         => (int) ($s->pending ?? 0),
                'approved'        => $approved,
                'rejected'        => (int) ($s->rejected ?? 0),
                'total'           => $total,
                'completion'      => $completion,
                'trend'           => $trend,
            ];

            $members = array_merge(
                $members,
                $this->buildTree($user->id, $statsMap, $prevMap, $activityMap, $level + 1)
            );
        }

        return $members;
    }

    // ── Public methods (called from blade) ────────────────────────────────────

    public function hasTeam(): bool
    {
        return User::where('manager_id', Auth::id())->exists();
    }

    public function getTeamMembers(): array
    {
        $userId = (int) Auth::id();
        $allIds = $this->allSubordinateIds($userId);

        if (empty($allIds)) return [];

        // Batch stats for selected period
        $statsMap = $this->applyPeriod(
            Business::whereIn('created_by_user', $allIds)
                ->selectRaw("
                    created_by_user,
                    SUM(CASE WHEN approval_status = 'DFT' THEN 1 ELSE 0 END) as draft,
                    SUM(CASE WHEN approval_status = 'PND' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN approval_status = 'APR' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN approval_status = 'REJ' THEN 1 ELSE 0 END) as rejected,
                    COUNT(*) as total
                ")
                ->groupBy('created_by_user')
        )->get()->keyBy('created_by_user')->toArray();

        // Batch stats for previous period (for trend) — only when a comparable prev period exists
        $prevMap = $this->hasPrevPeriod()
            ? $this->applyPrevPeriod(
                Business::whereIn('created_by_user', $allIds)
                    ->selectRaw("created_by_user, COUNT(*) as total")
                    ->groupBy('created_by_user')
            )->get()->keyBy('created_by_user')->toArray()
            : [];

        // Last activity per user (absolute, period-independent)
        $activityMap = Business::whereIn('created_by_user', $allIds)
            ->selectRaw("created_by_user, MAX(created_at) as last_at")
            ->groupBy('created_by_user')
            ->get()
            ->keyBy('created_by_user')
            ->toArray();

        return $this->buildTree($userId, $statsMap, $prevMap, $activityMap);
    }

    public function getKpis(): array
    {
        $userId  = (int) Auth::id();
        $allIds  = $this->allSubordinateIds($userId);
        $directIds = User::where('manager_id', $userId)->pluck('id')->toArray();

        $totals = empty($allIds) ? null :
            $this->applyPeriod(
                Business::whereIn('created_by_user', $allIds)
                    ->selectRaw("COUNT(*) as total, SUM(CASE WHEN approval_status = 'APR' THEN 1 ELSE 0 END) as approved")
            )->first();

        $pendingReview = empty($directIds) ? 0 :
            Business::whereIn('created_by_user', $directIds)->where('approval_status', 'PND')->count();

        return [
            'team_count'     => count($allIds),
            'pending_review' => $pendingReview,
            'registered'     => (int) ($totals->total    ?? 0),
            'approved'       => (int) ($totals->approved ?? 0),
        ];
    }

    public function getTopPerformer(): ?array
    {
        $userId = (int) Auth::id();
        $allIds = $this->allSubordinateIds($userId);
        if (empty($allIds)) return null;

        $top = $this->applyPeriod(
            Business::whereIn('created_by_user', $allIds)
                ->selectRaw("created_by_user, COUNT(*) as total")
                ->groupBy('created_by_user')
                ->orderByDesc('total')
        )->first();

        if (! $top || $top->total == 0) return null;

        $user = User::find($top->created_by_user);
        return [
            'name'     => $user?->name ?? '—',
            'initials' => $this->initials($user?->name ?? ''),
            'total'    => (int) $top->total,
        ];
    }

    public function getReinsurerDistribution(): array
    {
        $userId = (int) Auth::id();
        $allIds = $this->allSubordinateIds($userId);
        if (empty($allIds)) return [];

        $rows = $this->applyPeriod(
            Business::whereIn('businesses.created_by_user', $allIds)
                ->join('reinsurers', 'businesses.reinsurer_id', '=', 'reinsurers.id')
                ->selectRaw("reinsurers.name, COUNT(*) as total")
                ->groupBy('reinsurers.name')
                ->orderByDesc('total')
                ->limit(8),
            'businesses'
        )->get()->toArray();

        $max = collect($rows)->max('total') ?: 1;

        return array_map(fn($r) => [
            'name'  => $r['name'],
            'total' => $r['total'],
            'pct'   => round(($r['total'] / $max) * 100),
        ], $rows);
    }

    public function getMonthlyTrend(): array
    {
        $userId = (int) Auth::id();
        $allIds = $this->allSubordinateIds($userId);

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $count = empty($allIds) ? 0 :
                Business::whereIn('created_by_user', $allIds)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();

            $months[] = ['label' => $date->format('M'), 'count' => $count];
        }

        $max = collect($months)->max('count') ?: 1;

        return array_map(fn($m) => [
            ...$m,
            'pct' => round(($m['count'] / $max) * 100),
        ], $months);
    }

    public function getPendingReviews(): array
    {
        $userId    = (int) Auth::id();
        $directIds = User::where('manager_id', $userId)->pluck('id')->toArray();
        if (empty($directIds)) return [];

        return Business::whereIn('created_by_user', $directIds)
            ->where('approval_status', 'PND')
            ->with(['createdByUser:id,name', 'reinsurer:id,name'])
            ->orderBy('approval_status_updated_at')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'business_code' => $b->business_code,
                'description'   => $b->description,
                'submitter'     => $b->createdByUser?->name ?? '—',
                'reinsurer'     => $b->reinsurer?->name ?? '—',
                'days_ago'      => $b->approval_status_updated_at
                    ? (int) now()->diffInDays($b->approval_status_updated_at)
                    : null,
                'edit_url'      => route('filament.admin.resources.businesses.edit', $b),
            ])
            ->toArray();
    }

    public function getReinsurerTeamMatrix(): array
    {
        $userId = (int) Auth::id();
        $allIds = $this->allSubordinateIds($userId);
        if (empty($allIds)) return ['members' => [], 'rows' => []];

        // All team members ordered by name (flat list for column headers)
        $members = User::whereIn('id', $allIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($u) => [
                'id'       => $u->id,
                'name'     => $u->name,
                'initials' => $this->initials($u->name),
            ])
            ->toArray();

        // Raw counts: reinsurer × user
        $rows = $this->applyPeriod(
            Business::whereIn('businesses.created_by_user', $allIds)
                ->join('reinsurers', 'businesses.reinsurer_id', '=', 'reinsurers.id')
                ->selectRaw('reinsurers.id as reinsurer_id, reinsurers.name as reinsurer_name, businesses.created_by_user, COUNT(*) as cnt')
                ->groupBy('reinsurers.id', 'reinsurers.name', 'businesses.created_by_user'),
            'businesses'
        )->get();

        // Pivot into [ reinsurer_id => [ user_id => count ] ]
        $pivot  = [];
        $totals = [];

        foreach ($rows as $row) {
            $rId  = $row->reinsurer_id;
            $uId  = $row->created_by_user;
            $pivot[$rId]['name']        = $row->reinsurer_name;
            $pivot[$rId]['counts'][$uId] = (int) $row->cnt;
            $totals[$rId]               = ($totals[$rId] ?? 0) + (int) $row->cnt;
        }

        // Sort by total descending
        arsort($totals);

        $maxTotal = max(array_values($totals) ?: [1]);

        $matrixRows = [];
        foreach ($totals as $rId => $total) {
            $counts = $pivot[$rId]['counts'] ?? [];
            $matrixRows[] = [
                'reinsurer' => $pivot[$rId]['name'],
                'total'     => $total,
                'heat'      => round(($total / $maxTotal) * 100), // 0-100 for row heat
                'counts'    => $counts, // keyed by user_id
            ];
        }

        return ['members' => $members, 'rows' => $matrixRows];
    }

    public function getPeriodLabel(): string
    {
        if ($this->period === 'month')   return now()->format('F Y');
        if ($this->period === 'year')    return now()->format('Y');
        if ($this->period === 'quarter') {
            $q = $this->quarter ?? now()->quarter;
            return "Q{$q} " . now()->year;
        }
        if ($this->period === 'custom') {
            $from = $this->dateFrom ? \Carbon\Carbon::parse($this->dateFrom . '-01')->format('M Y') : '…';
            $to   = $this->dateTo   ? \Carbon\Carbon::parse($this->dateTo   . '-01')->format('M Y') : '…';
            return "{$from} → {$to}";
        }
        return 'All Time';
    }
}
