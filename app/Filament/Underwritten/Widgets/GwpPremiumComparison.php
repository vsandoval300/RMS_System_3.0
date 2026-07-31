<?php

namespace App\Filament\Underwritten\Widgets;

use App\Models\Business;
use App\Models\OperativeDoc;
use App\Models\Reinsurer;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class GwpPremiumComparison extends Widget
{
    protected string $view = 'filament.widgets.gwp-premium-comparison';

    public int    $selectedYear;
    public ?int   $selectedReinsurer   = null;
    public ?int   $selectedRetrocedant = null;
    public ?int   $selectedCedant      = null;
    public string $sortColumn = 'code';

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public function updatedSelectedYear(): void
    {
        $this->dispatchFiltersUpdated();
    }

    // Each filter resets the other two (mutually exclusive)
    public function updatedSelectedReinsurer(): void
    {
        $this->selectedRetrocedant = null;
        $this->selectedCedant      = null;
        $this->dispatchFiltersUpdated();
    }

    public function updatedSelectedRetrocedant(): void
    {
        $this->selectedReinsurer = null;
        $this->selectedCedant    = null;
        $this->dispatchFiltersUpdated();
    }

    public function updatedSelectedCedant(): void
    {
        $this->selectedReinsurer   = null;
        $this->selectedRetrocedant = null;
        $this->dispatchFiltersUpdated();
    }

    public function setSortColumn(string $column): void
    {
        $this->sortColumn = $column;
    }

    private function dispatchFiltersUpdated(): void
    {
        $this->dispatch('gwp-filters-updated',
            year:      $this->selectedYear,
            reinsurer: $this->selectedReinsurer,
        );
    }

    public function getRetrocedants(): array
    {
        // Retrocedant = type-5 destination whose source is NOT type-5 (handles broker intermediaries)
        return DB::table('cost_nodesx as cn')
            ->join('partners as p',  'p.id',  '=', 'cn.partner_destination_id')
            ->join('partners as ps', 'ps.id', '=', 'cn.partner_source_id')
            ->where('p.partner_types_id', 5)
            ->where('ps.partner_types_id', '!=', 5)
            ->whereNull('cn.deleted_at')
            ->selectRaw('p.id, COALESCE(p.short_name, p.name) as label')
            ->distinct()
            ->orderByRaw('COALESCE(p.short_name, p.name)')
            ->pluck('label', 'id')
            ->toArray();
    }

    public function getCedants(): array
    {
        // Cedant = type-3 partner that appears as SOURCE in any node (works when chain starts directly with cedant)
        return DB::table('cost_nodesx as cn')
            ->join('partners as p', 'p.id', '=', 'cn.partner_source_id')
            ->where('p.partner_types_id', 3)
            ->whereNull('cn.deleted_at')
            ->selectRaw('p.id, COALESCE(p.short_name, p.name) as label')
            ->distinct()
            ->orderByRaw('COALESCE(p.short_name, p.name)')
            ->pluck('label', 'id')
            ->toArray();
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

    public function getCedantFee(): float
    {
        $year = $this->selectedYear;

        $extraWhere = '';
        $bindings   = [$year];

        if ($this->selectedReinsurer) {
            $extraWhere = 'AND b.reinsurer_id = ?';
            $bindings[] = $this->selectedReinsurer;
        } elseif ($this->selectedRetrocedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_destination_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedRetrocedant;
        } elseif ($this->selectedCedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_source_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedCedant;
        }

        $result = DB::select("
            WITH scheme_fts AS (
                SELECT
                    i.cscheme_id AS scheme_id,
                    ROUND((
                        SUM(i.premium::numeric)
                        / CASE WHEN EXTRACT(YEAR FROM d.inception_date)::int % 4 = 0
                               THEN 366 ELSE 365 END
                        * DATE_PART('day', d.expiration_date - d.inception_date)::numeric
                        * cs.share::numeric
                        / NULLIF(d.roe_fs::numeric, 0)
                    ), 4) AS fts
                FROM operative_docs d
                JOIN businessdoc_insureds i  ON i.op_document_id = d.id AND i.deleted_at IS NULL
                JOIN businessdoc_schemes   s ON s.op_document_id = d.id
                                            AND s.cscheme_id = i.cscheme_id
                                            AND s.deleted_at IS NULL
                JOIN cost_schemes cs         ON cs.id = i.cscheme_id AND cs.deleted_at IS NULL
                JOIN businesses   b          ON b.business_code = d.business_code
                                            AND b.deleted_at IS NULL
                                            AND b.approval_status = 'APR'
                WHERE EXTRACT(YEAR FROM d.rep_date) = ?
                {$extraWhere}
                GROUP BY d.id, i.cscheme_id, d.inception_date, d.expiration_date, cs.share, d.roe_fs
            )
            SELECT ROUND(COALESCE(SUM(sf.fts * cn.value::numeric), 0)::numeric, 2) AS total_fee
            FROM scheme_fts sf
            JOIN cost_nodesx cn ON cn.cscheme_id = sf.scheme_id AND cn.deleted_at IS NULL
            WHERE cn.concept IN (
                SELECT id FROM deductions WHERE concept IN ('fee', 'referral') AND deleted_at IS NULL
            )
        ", $bindings);

        return (float) ($result[0]->total_fee ?? 0);
    }

    public function getRetrocedantFee(): float
    {
        $year = $this->selectedYear;

        $extraWhere = '';
        $bindings   = [$year];

        if ($this->selectedReinsurer) {
            $extraWhere = 'AND b.reinsurer_id = ?';
            $bindings[] = $this->selectedReinsurer;
        } elseif ($this->selectedRetrocedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_destination_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedRetrocedant;
        } elseif ($this->selectedCedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_source_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedCedant;
        }

        $result = DB::select("
            WITH scheme_fts AS (
                SELECT
                    i.cscheme_id AS scheme_id,
                    ROUND((
                        SUM(i.premium::numeric)
                        / CASE WHEN EXTRACT(YEAR FROM d.inception_date)::int % 4 = 0
                               THEN 366 ELSE 365 END
                        * DATE_PART('day', d.expiration_date - d.inception_date)::numeric
                        * cs.share::numeric
                        / NULLIF(d.roe_fs::numeric, 0)
                    ), 4) AS fts
                FROM operative_docs d
                JOIN businessdoc_insureds i  ON i.op_document_id = d.id AND i.deleted_at IS NULL
                JOIN businessdoc_schemes   s ON s.op_document_id = d.id
                                            AND s.cscheme_id = i.cscheme_id
                                            AND s.deleted_at IS NULL
                JOIN cost_schemes cs         ON cs.id = i.cscheme_id AND cs.deleted_at IS NULL
                JOIN businesses   b          ON b.business_code = d.business_code
                                            AND b.deleted_at IS NULL
                                            AND b.approval_status = 'APR'
                WHERE EXTRACT(YEAR FROM d.rep_date) = ?
                {$extraWhere}
                GROUP BY d.id, i.cscheme_id, d.inception_date, d.expiration_date, cs.share, d.roe_fs
            )
            SELECT ROUND(COALESCE(SUM(sf.fts * cn.value::numeric), 0)::numeric, 2) AS total_fee
            FROM scheme_fts sf
            JOIN cost_nodesx cn  ON cn.cscheme_id = sf.scheme_id AND cn.deleted_at IS NULL
            JOIN partners    pd  ON pd.id = cn.partner_destination_id AND pd.partner_types_id = 5
            JOIN partners    ps  ON ps.id = cn.partner_source_id      AND ps.partner_types_id != 5
            WHERE cn.concept IN (
                SELECT id FROM deductions WHERE concept = 'fee' AND deleted_at IS NULL
            )
        ", $bindings);

        return (float) ($result[0]->total_fee ?? 0);
    }

    public function getRetrocedantReferralFee(): float
    {
        $year = $this->selectedYear;

        $extraWhere = '';
        $bindings   = [$year];

        if ($this->selectedReinsurer) {
            $extraWhere = 'AND b.reinsurer_id = ?';
            $bindings[] = $this->selectedReinsurer;
        } elseif ($this->selectedRetrocedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_destination_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedRetrocedant;
        } elseif ($this->selectedCedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_source_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedCedant;
        }

        $result = DB::select("
            WITH scheme_fts AS (
                SELECT
                    i.cscheme_id AS scheme_id,
                    ROUND((
                        SUM(i.premium::numeric)
                        / CASE WHEN EXTRACT(YEAR FROM d.inception_date)::int % 4 = 0
                               THEN 366 ELSE 365 END
                        * DATE_PART('day', d.expiration_date - d.inception_date)::numeric
                        * cs.share::numeric
                        / NULLIF(d.roe_fs::numeric, 0)
                    ), 4) AS fts
                FROM operative_docs d
                JOIN businessdoc_insureds i  ON i.op_document_id = d.id AND i.deleted_at IS NULL
                JOIN businessdoc_schemes   s ON s.op_document_id = d.id
                                            AND s.cscheme_id = i.cscheme_id
                                            AND s.deleted_at IS NULL
                JOIN cost_schemes cs         ON cs.id = i.cscheme_id AND cs.deleted_at IS NULL
                JOIN businesses   b          ON b.business_code = d.business_code
                                            AND b.deleted_at IS NULL
                                            AND b.approval_status = 'APR'
                WHERE EXTRACT(YEAR FROM d.rep_date) = ?
                {$extraWhere}
                GROUP BY d.id, i.cscheme_id, d.inception_date, d.expiration_date, cs.share, d.roe_fs
            )
            SELECT ROUND(COALESCE(SUM(sf.fts * cn.value::numeric), 0)::numeric, 2) AS total_fee
            FROM scheme_fts sf
            JOIN cost_nodesx cn  ON cn.cscheme_id = sf.scheme_id AND cn.deleted_at IS NULL
            JOIN partners    pd  ON pd.id = cn.partner_destination_id AND pd.partner_types_id = 5
            JOIN partners    ps  ON ps.id = cn.partner_source_id      AND ps.partner_types_id != 5
            WHERE cn.concept IN (
                SELECT id FROM deductions WHERE concept = 'referral' AND deleted_at IS NULL
            )
        ", $bindings);

        return (float) ($result[0]->total_fee ?? 0);
    }

    public function getManagementFee(): float
    {
        $year = $this->selectedYear;

        $extraWhere = '';
        $bindings   = [$year];

        if ($this->selectedReinsurer) {
            $extraWhere = 'AND b.reinsurer_id = ?';
            $bindings[] = $this->selectedReinsurer;
        } elseif ($this->selectedRetrocedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_destination_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedRetrocedant;
        } elseif ($this->selectedCedant) {
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_source_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedCedant;
        }

        $result = DB::select("
            WITH scheme_fts AS (
                SELECT
                    i.cscheme_id AS scheme_id,
                    ROUND((
                        SUM(i.premium::numeric)
                        / CASE WHEN EXTRACT(YEAR FROM d.inception_date)::int % 4 = 0
                               THEN 366 ELSE 365 END
                        * DATE_PART('day', d.expiration_date - d.inception_date)::numeric
                        * cs.share::numeric
                        / NULLIF(d.roe_fs::numeric, 0)
                    ), 4) AS fts
                FROM operative_docs d
                JOIN businessdoc_insureds i  ON i.op_document_id = d.id AND i.deleted_at IS NULL
                JOIN businessdoc_schemes   s ON s.op_document_id = d.id
                                            AND s.cscheme_id = i.cscheme_id
                                            AND s.deleted_at IS NULL
                JOIN cost_schemes cs         ON cs.id = i.cscheme_id AND cs.deleted_at IS NULL
                JOIN businesses   b          ON b.business_code = d.business_code
                                            AND b.deleted_at IS NULL
                                            AND b.approval_status = 'APR'
                WHERE EXTRACT(YEAR FROM d.rep_date) = ?
                {$extraWhere}
                GROUP BY d.id, i.cscheme_id, d.inception_date, d.expiration_date, cs.share, d.roe_fs
            )
            SELECT ROUND(COALESCE(SUM(sf.fts * cn.value::numeric), 0)::numeric, 2) AS total_fee
            FROM scheme_fts sf
            JOIN cost_nodesx cn ON cn.cscheme_id = sf.scheme_id AND cn.deleted_at IS NULL
        ", $bindings);

        return (float) ($result[0]->total_fee ?? 0);
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

    public function getRetrocedenteData(): array
    {
        $year = $this->selectedYear;

        // Build optional filter clause (mutually exclusive with reinsurer/cedant)
        $extraWhere = '';
        $bindings   = [$year];

        if ($this->selectedReinsurer) {
            $extraWhere = 'AND b.reinsurer_id = ?';
            $bindings[] = $this->selectedReinsurer;
        } elseif ($this->selectedRetrocedant) {
            // Retrocedant appears as destination
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_destination_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedRetrocedant;
        } elseif ($this->selectedCedant) {
            // Cedant appears as source
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_source_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedCedant;
        }

        $rows = DB::select("
            WITH scheme_fts AS (
                SELECT
                    d.id         AS doc_id,
                    i.cscheme_id AS scheme_id,
                    ROUND((
                        SUM(i.premium::numeric)
                        / CASE WHEN EXTRACT(YEAR FROM d.inception_date)::int % 4 = 0
                               THEN 366 ELSE 365 END
                        * DATE_PART('day', d.expiration_date - d.inception_date)::numeric
                        * cs.share::numeric
                        / NULLIF(d.roe_fs::numeric, 0)
                    ), 4) AS fts
                FROM operative_docs d
                JOIN businessdoc_insureds i  ON i.op_document_id = d.id AND i.deleted_at IS NULL
                JOIN businessdoc_schemes   s ON s.op_document_id = d.id
                                            AND s.cscheme_id = i.cscheme_id
                                            AND s.deleted_at IS NULL
                JOIN cost_schemes cs         ON cs.id = i.cscheme_id AND cs.deleted_at IS NULL
                JOIN businesses   b          ON b.business_code = d.business_code
                                            AND b.deleted_at IS NULL
                                            AND b.approval_status = 'APR'
                WHERE EXTRACT(YEAR FROM d.rep_date) = ?
                {$extraWhere}
                GROUP BY d.id, i.cscheme_id, d.inception_date, d.expiration_date, cs.share, d.roe_fs
            ),
            scheme_partner AS (
                -- Retrocedant = first type-5 destination whose source is NOT another type-5.
                -- This handles chains with a type-4 broker between cedant and retrocedant.
                SELECT DISTINCT ON (cn.cscheme_id)
                    cn.cscheme_id,
                    p.id                           AS partner_id,
                    COALESCE(p.short_name, p.name) AS pname
                FROM cost_nodesx cn
                JOIN partners p  ON p.id  = cn.partner_destination_id AND p.partner_types_id = 5
                JOIN partners ps ON ps.id = cn.partner_source_id      AND ps.partner_types_id != 5
                WHERE cn.deleted_at IS NULL
                ORDER BY cn.cscheme_id, cn.index
            )
            SELECT
                sp.partner_id                  AS pid,
                sp.pname,
                ROUND(SUM(sf.fts)::numeric, 4) AS gwp
            FROM scheme_fts sf
            JOIN scheme_partner sp ON sp.cscheme_id = sf.scheme_id
            GROUP BY sp.partner_id, sp.pname
        ", $bindings);

        if (empty($rows)) {
            return [];
        }

        $rows = array_filter($rows, fn ($r) => $r->gwp > 0);
        usort($rows, fn ($a, $b) => $b->gwp <=> $a->gwp);

        $total = array_sum(array_column($rows, 'gwp'));

        return array_values(array_map(fn ($r) => [
            'pid'  => (int)   $r->pid,
            'name' => $r->pname,
            'gwp'  => (float) $r->gwp,
            'pct'  => $total > 0 ? round($r->gwp / $total * 100, 1) : 0,
        ], $rows));
    }

    public function getCedantData(): array
    {
        $year = $this->selectedYear;

        // Same two-step CTE as getRetrocedenteData(), but grouping by Cedant(type3)
        // whose source in cost_nodesx is Client(type7).
        $extraWhere = '';
        $bindings   = [$year];

        if ($this->selectedReinsurer) {
            $extraWhere = 'AND b.reinsurer_id = ?';
            $bindings[] = $this->selectedReinsurer;
        } elseif ($this->selectedRetrocedant) {
            // Retrocedant appears as destination
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_destination_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedRetrocedant;
        } elseif ($this->selectedCedant) {
            // Cedant appears as source
            $extraWhere = 'AND EXISTS (SELECT 1 FROM cost_nodesx cn_f WHERE cn_f.cscheme_id = i.cscheme_id AND cn_f.partner_source_id = ? AND cn_f.deleted_at IS NULL)';
            $bindings[] = $this->selectedCedant;
        }

        $rows = DB::select("
            WITH scheme_fts AS (
                SELECT
                    d.id         AS doc_id,
                    i.cscheme_id AS scheme_id,
                    ROUND((
                        SUM(i.premium::numeric)
                        / CASE WHEN EXTRACT(YEAR FROM d.inception_date)::int % 4 = 0
                               THEN 366 ELSE 365 END
                        * DATE_PART('day', d.expiration_date - d.inception_date)::numeric
                        * cs.share::numeric
                        / NULLIF(d.roe_fs::numeric, 0)
                    ), 4) AS fts
                FROM operative_docs d
                JOIN businessdoc_insureds i  ON i.op_document_id = d.id AND i.deleted_at IS NULL
                JOIN businessdoc_schemes   s ON s.op_document_id = d.id
                                            AND s.cscheme_id = i.cscheme_id
                                            AND s.deleted_at IS NULL
                JOIN cost_schemes cs         ON cs.id = i.cscheme_id AND cs.deleted_at IS NULL
                JOIN businesses   b          ON b.business_code = d.business_code
                                            AND b.deleted_at IS NULL
                                            AND b.approval_status = 'APR'
                WHERE EXTRACT(YEAR FROM d.rep_date) = ?
                {$extraWhere}
                GROUP BY d.id, i.cscheme_id, d.inception_date, d.expiration_date, cs.share, d.roe_fs
            ),
            scheme_partner AS (
                -- Cedant = type-3 partner that appears as SOURCE in any node.
                -- Handles chains that start directly with the cedant (no Client node preceding them).
                SELECT DISTINCT ON (cn.cscheme_id)
                    cn.cscheme_id,
                    p.id                           AS partner_id,
                    COALESCE(p.short_name, p.name) AS pname
                FROM cost_nodesx cn
                JOIN partners p ON p.id = cn.partner_source_id AND p.partner_types_id = 3
                WHERE cn.deleted_at IS NULL
                ORDER BY cn.cscheme_id, cn.index
            )
            SELECT
                sp.partner_id                  AS pid,
                sp.pname,
                ROUND(SUM(sf.fts)::numeric, 4) AS gwp
            FROM scheme_fts sf
            JOIN scheme_partner sp ON sp.cscheme_id = sf.scheme_id
            GROUP BY sp.partner_id, sp.pname
        ", $bindings);

        if (empty($rows)) {
            return [];
        }

        $rows = array_filter($rows, fn ($r) => $r->gwp > 0);
        usort($rows, fn ($a, $b) => $b->gwp <=> $a->gwp);

        $total = array_sum(array_column($rows, 'gwp'));

        return array_values(array_map(fn ($r) => [
            'pid'  => (int)   $r->pid,
            'name' => $r->pname,
            'gwp'  => (float) $r->gwp,
            'pct'  => $total > 0 ? round($r->gwp / $total * 100, 1) : 0,
        ], $rows));
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
            ->when($this->selectedRetrocedant, fn ($q) =>
                $q->whereHas('schemes', fn ($s) =>
                    $s->whereHas('costScheme', fn ($cs) =>
                        $cs->whereHas('costNodexes', fn ($cn) =>
                            $cn->where('partner_destination_id', $this->selectedRetrocedant)
                               ->whereNull('deleted_at')
                        )
                    )
                )
            )
            ->when($this->selectedCedant, fn ($q) =>
                $q->whereHas('schemes', fn ($s) =>
                    $s->whereHas('costScheme', fn ($cs) =>
                        $cs->whereHas('costNodexes', fn ($cn) =>
                            $cn->where('partner_source_id', $this->selectedCedant)
                               ->whereNull('deleted_at')
                        )
                    )
                )
            )
            ->get();

        $byReinsurer = [];

        foreach ($docs as $doc) {
            $docYear     = Carbon::parse($doc->rep_date)->year;
            $reinsurer   = $doc->business?->reinsurer;
            $name        = $reinsurer?->short_name ?? 'Unknown';
            $cnsCode     = $reinsurer?->cns_reinsurer ?? $reinsurer?->id ?? '';

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

        $deltas      = array_values(array_map(fn ($r) => abs($r['ac'] - $r['pl']), $byReinsurer));
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
