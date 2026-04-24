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
            ->when(!empty($this->reinsurerIds), fn ($q) =>
                $q->whereIn('businesses.reinsurer_id', $this->reinsurerIds)
            )
            ->whereBetween($this->dateColumn, [$this->dateStart, $this->dateEnd])

            ->leftJoin('businessdoc_insureds', 'businessdoc_insureds.op_document_id', '=', 'operative_docs.id')
            ->leftJoin('companies', 'companies.id', '=', 'businessdoc_insureds.company_id')
            ->leftJoin('countries', 'countries.id', '=', 'companies.country_id')
            ->leftJoin('coverages', 'coverages.id', '=', 'businessdoc_insureds.coverage_id')

            ->leftJoin('cost_schemes as insured_scheme', 'insured_scheme.id', '=', 'businessdoc_insureds.cscheme_id')
            ->leftJoin('cost_nodesx', 'cost_nodesx.cscheme_id', '=', 'insured_scheme.id')
            ->leftJoin('deductions', 'deductions.id', '=', 'cost_nodesx.concept')
            ->leftJoin('partners as p_src', 'p_src.id', '=', 'cost_nodesx.partner_source_id')

            ->orderBy('businessdoc_insureds.id')
            ->orderBy('cost_nodesx.index')

            ->select([
                'operative_docs.*',
                'operative_docs.rep_date as rep_date',
                'users.initials as created_by_initials',

                'businesses.source_code as business_source_code',
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

                'cost_nodesx.id as node_id',
                'cost_nodesx.index as node_index',
                'cost_nodesx.value as node_value',
                'cost_nodesx.apply_to_gross as node_apply_to_gross',

                'deductions.concept as deduction_concept',
                'p_src.name as node_source_name',
                'p_src.acronym as node_source_acronym',
            ]);

        // 🔥 streaming real sin closures serializados
        $rows = $query->cursor();

        $grouped = [];
        $result = [];

        foreach ($rows as $row) {

            $key = $row->insured_row_id ?? ($row->id . '|no-insured');

            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }

            $grouped[$key][] = $row;

            // 🔥 flush para no crecer infinito
            if (count($grouped[$key]) > 50) {
                $result[] = $this->buildRow(collect($grouped[$key]));
                unset($grouped[$key]);
            }
        }

        foreach ($grouped as $rowsGroup) {
            $result[] = $this->buildRow(collect($rowsGroup));
        }

        $maxNodes = collect($result)->max(fn ($r) => count($r->nodes_list ?? [])) ?? 0;

        Excel::store(
            new OperativeDocsExport(collect($result), $maxNodes),
            $this->filename,
            'public'
        );

        NotifyReportReady::dispatch(
            $this->userId,
            //$this->path,
            $this->filename
        );
    }

    private function buildRow(Collection $rows)
    {
        $first = $rows->first();

        $nodes = [];

        foreach ($rows as $r) {
            if (!$r->node_id) continue;

            $nodes[] = [
                'deduction_type' => $r->deduction_concept,
                'source' => $r->node_source_name,
                'value' => $r->node_value,
                'amount_oc' => 0, // simplificado
            ];
        }

        $first->nodes_list = $nodes;

        return $first;
    }
}
