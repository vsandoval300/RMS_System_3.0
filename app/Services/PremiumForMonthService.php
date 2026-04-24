<?php

namespace App\Services;

use App\Models\OperativeDoc;
use App\Models\CostScheme;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PremiumForMonthService
{
    public function mensualFTS(?int $reinsurerId = null, ?int $year = null): array
    {
        $query = DB::table('operative_docs as od')
            ->join('businessdoc_insureds as i', 'i.op_document_id', '=', 'od.id')
            ->leftJoin('businessdoc_schemes as s', 's.op_document_id', '=', 'od.id')
            ->leftJoin('cost_schemes as cs', 'cs.id', '=', 's.cscheme_id')

            ->when($reinsurerId, function ($q) use ($reinsurerId) {
                $q->join('businesses as b', 'b.business_code', '=', 'od.business_code')
                ->where('b.reinsurer_id', $reinsurerId);
            })

            ->when($year, function ($q) use ($year) {
                $q->whereRaw('EXTRACT(YEAR FROM od.rep_date) = ?', [$year]);
            })

            ->selectRaw("
                DATE_TRUNC('month', od.rep_date) as month_date,
                TO_CHAR(DATE_TRUNC('month', od.rep_date), 'Mon') as month_label,

                SUM(
                    (
                        (i.premium /
                            (CASE 
                                WHEN EXTRACT(YEAR FROM od.inception_date) % 4 = 0 
                                THEN 366 
                                ELSE 365 
                            END)
                        )
                        *
                        (od.expiration_date - od.inception_date)
                    )
                ) as ftp,

                SUM(
                    (
                        (
                            (i.premium /
                                (CASE 
                                    WHEN EXTRACT(YEAR FROM od.inception_date) % 4 = 0 
                                    THEN 366 
                                    ELSE 365 
                                END)
                            )
                            *
                            (od.expiration_date - od.inception_date)
                        )
                        * COALESCE(cs.share, 0)
                    )
                    / NULLIF(od.roe_fs, 0)
                ) as fts
            ")

            ->groupByRaw("DATE_TRUNC('month', od.rep_date)")
            ->orderByRaw("DATE_TRUNC('month', od.rep_date)");

        $results = $query->get();

        return [
            'labels' => $results->pluck('month_label'), // 👈 Jan, Feb, Mar
            'ftp'    => $results->pluck('ftp'),
            'fts'    => $results->pluck('fts'),
        ];
    }
}