<?php

namespace App\Jobs;

use App\Exports\OperativeDocsExport;
use App\Jobs\NotifyReportReady;
use App\Models\OperativeDoc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GenerateOperativeDocsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;

    protected $dateColumn;
    protected $dateStart;
    protected $dateEnd;
    protected $reinsurerIds;
    //protected $path;
    protected $filename;
    protected $userId;

    public function __construct(
        $dateColumn,
        $dateStart,
        $dateEnd,
        $reinsurerIds,
        //$path,
        $filename,
        $userId
    ) {
        $this->dateColumn = $dateColumn;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->reinsurerIds = $reinsurerIds;
        //$this->path = $path;
        $this->filename = $filename;
        $this->userId = $userId;
    }

    public function handle()
    {
        $query = OperativeDoc::query()
            ->whereNull('operative_docs.deleted_at')
            ->whereNull('businesses.deleted_at')
            ->whereNull('businessdoc_insureds.deleted_at')
            ->with([
                'business.reinsurer',
                'business.currency',
                'business.liabilityStructures',
                'business.producer',
                'docType',
            ])
            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
            ->leftJoin('users', 'users.id', '=', 'operative_docs.created_by_user')
            ->leftJoin('partners as producer_partner', 'producer_partner.id', '=', 'businesses.producer_id')
            ->when(count($this->reinsurerIds) > 0, function ($q) {
                $q->whereIn('businesses.reinsurer_id', $this->reinsurerIds);
            })
            
            ->whereBetween($this->dateColumn, [$this->dateStart, $this->dateEnd])

            ->leftJoin('businessdoc_insureds', 'businessdoc_insureds.op_document_id', '=', 'operative_docs.id')
            ->leftJoin('companies', 'companies.id', '=', 'businessdoc_insureds.company_id')
            ->leftJoin('countries', 'countries.id', '=', 'companies.country_id')
            ->leftJoin('coverages', 'coverages.id', '=', 'businessdoc_insureds.coverage_id')

            ->leftJoin('cost_schemes as insured_scheme', 'insured_scheme.id', '=', 'businessdoc_insureds.cscheme_id')
            ->leftJoin('cost_nodesx', 'cost_nodesx.cscheme_id', '=', 'insured_scheme.id')
            ->leftJoin('deductions', 'deductions.id', '=', 'cost_nodesx.concept')
            ->leftJoin('partners as p_src', 'p_src.id', '=', 'cost_nodesx.partner_source_id')
            
            ->orderBy('businesses.business_code')
            ->orderBy('operative_docs.id')
            ->orderBy('businessdoc_insureds.id')
            ->orderBy('cost_nodesx.index')

            ->select([
                'operative_docs.*',
                'operative_docs.rep_date as rep_date',
                'users.initials as created_by_initials',

                'businesses.source_code as business_source_code',
                'businesses.policy_number as business_policy_number',
                'businesses.parent_id as business_parent_id',
                'businesses.renewed_from_id as business_renewed_from_id',
                'producer_partner.name as producer_name',

                'insured_scheme.share as share',

                'companies.name as insured_name',
                'countries.name as country_name',
                'coverages.name as coverage_name',
                'businessdoc_insureds.premium as insured_premium',

                'businessdoc_insureds.id as insured_row_id',
                'businessdoc_insureds.cscheme_id as insured_cscheme_id',

                'cost_nodesx.cscheme_id as node_cscheme_id',
                'cost_nodesx.id as node_id',
                'cost_nodesx.index as node_index',
                'cost_nodesx.value as node_value',
                'cost_nodesx.apply_to_gross as node_apply_to_gross',

                'deductions.concept as deduction_concept',
                'p_src.name as node_source_name',
                'p_src.acronym as node_source_acronym',
            ]);

        // Note: the query is already ordered by business_code, then
        // operative_docs.id, then businessdoc_insureds.id (the grouping
        // key below), then cost_nodesx.index — so rows belonging to the
        // same group always arrive contiguously, and the output already
        // comes out sorted by Business Code / OperativeDoc ID with no
        // extra PHP-side sort needed.
        $maxNodes = $this->calculateMaxNodes();
        $path = 'uw-reports/' . $this->filename;

        Excel::store(
            new OperativeDocsExport($this->streamRows($query), $maxNodes),
            $path
        );

        NotifyReportReady::dispatch(
            $this->userId,
            $this->filename
        );
    }

    /**
     * Widest Cost Scheme (by node count) across the whole catalog, used to
     * size the Node_N_* column blocks. Computed independently of the
     * (potentially huge, fanned-out) report query so it stays cheap even
     * for a full-history date range.
     */
    private function calculateMaxNodes(): int
    {
        $widest = DB::table('cost_nodesx')
            ->select('cscheme_id', DB::raw('COUNT(*) as node_count'))
            ->groupBy('cscheme_id')
            ->orderByDesc('node_count')
            ->first();

        return (int) ($widest->node_count ?? 0);
    }

    /**
     * Streams the report row-by-row instead of materializing the entire
     * (fanned-out) result set in memory before writing anything — a full
     * date-range export can otherwise hold hundreds of thousands of raw
     * joined rows in PHP at once. Rows for the same insured are grouped
     * as they arrive, relying on the query's ORDER BY to guarantee they
     * are contiguous, and each finished group is yielded to the Excel
     * writer immediately via buildRow() (unchanged calculation logic).
     */
    private function streamRows($query): \Generator
    {
        $currentKey = null;
        $currentGroup = [];

        foreach ($query->cursor() as $row) {

            $key = $row->insured_row_id ?? ($row->id . '|no-insured');

            if ($currentKey !== null && $key !== $currentKey) {
                yield $this->buildRow(collect($currentGroup));
                $currentGroup = [];
            }

            $currentKey = $key;
            $currentGroup[] = $row;
        }

        if (!empty($currentGroup)) {
            yield $this->buildRow(collect($currentGroup));
        }
    }

    private function buildRow(Collection $rows)
    {
        $first = $rows->first();

        $schemeId = $first->insured_cscheme_id;

        $schemeNodes = $rows
            ->filter(fn ($r) => $schemeId && ($r->insured_cscheme_id ?? null) === $schemeId)
            ->unique('node_id')
            ->sortBy(fn ($r) => (int) ($r->node_index ?? 0))
            ->values();

        // ==========================
        // BASE CALC (igual que export)
        // ==========================
        $inception  = $first->inception_date ?? null;
        $expiration = $first->expiration_date ?? null;

        $coverageDays = ($inception && $expiration)
            ? \Carbon\Carbon::parse($inception)->diffInDays(\Carbon\Carbon::parse($expiration))
            : 0;

        $premiumOc = (float) ($first->insured_premium ?? 0);

        $premiumFtpOc = ($coverageDays > 0)
            ? ($premiumOc / 365) * (float) $coverageDays
            : 0.0;

        $share = (float) ($first->share ?? 0);
        $share = ($share > 1) ? ($share / 100) : $share;

        $gwpFtsOc = round($premiumFtpOc * $share, 2);

        // ==========================
        // NODES CALC
        // ==========================
        $runningBase = $gwpFtsOc;

        $nodes = [];

        foreach ($schemeNodes as $r) {

            if (is_null($r->node_id)) continue;

            $rate = is_null($r->node_value) ? 0.0 : (float) $r->node_value;
            $rate = ($rate > 1) ? ($rate / 100) : $rate;

            $applyToGross = (bool) ($r->node_apply_to_gross ?? false);

            $baseForNode = $applyToGross ? $runningBase : $gwpFtsOc;

            $amountOc = round($baseForNode * $rate, 2);

            $runningBase = round($runningBase - $amountOc, 2);

            $nodes[] = [
                'deduction_type' => $r->deduction_concept ?? null,
                'source' => $r->node_source_name ?? null,
                'value' => $rate,
                'apply_to_gross' => $applyToGross,
                'amount_oc' => $amountOc,
            ];
        }

        $first->nodes_list = $nodes;

        return $first;
    }

}
