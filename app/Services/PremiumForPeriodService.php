<?php

namespace App\Services;

use App\Models\OperativeDoc;
use App\Models\CostScheme;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PremiumForPeriodService
{
    public function anualFTS(?int $reinsurerId = null, ?int $year = null): array
    {
        $query = OperativeDoc::query()
            ->whereYear('rep_date', '>=', 2010)
            ->with([
                'docType',
                'business.currency',
                'schemes.costScheme.costNodexes.partnerSource',
                'schemes.costScheme.costNodexes.deduction',
                'insureds.company.country',
                'insureds.coverage',
                'transactions.logs.toPartner',
            ])

             // filtra solo si $reinsurerId tiene valor
            ->when($reinsurerId, fn($q) => $q->whereHas('business', fn($b) => $b->where('reinsurer_id', $reinsurerId)))
            // filtra solo si $year tiene valor
            ->when($year, fn($q) => $q->whereYear('rep_date', $year));

            /* if ($reinsurerId !== null) {
                $query->whereHas('business', function ($q) use ($reinsurerId) {
                    $q->where('reinsurer_id', $reinsurerId);
                });
            } */

        $docs = $query->get();
        
        $grouped = [];

        foreach ($docs as $doc) {

            $year = \Carbon\Carbon::parse($doc->rep_date)->year;

            $inception = \Carbon\Carbon::parse($doc->inception_date);
            $expiration = \Carbon\Carbon::parse($doc->expiration_date);

            $daysInYear = $inception->isLeapYear() ? 366 : 365;
            $coverageDays = $inception->diffInDays($expiration);

            $totalPremium = $doc->insureds->sum('premium');

            $ftp = ($daysInYear > 0)
                ? ($totalPremium / $daysInYear) * $coverageDays
                : 0;
            
            $fts = 0;

            foreach ($doc->insureds as $insured) {

                $share = optional(
                    $doc->schemes
                        ->firstWhere('costScheme.id', $insured->cscheme_id)
                )->costScheme->share ?? 0;

                $ftpIndividual = ($daysInYear > 0)
                    ? ($insured->premium / $daysInYear) * $coverageDays
                    : 0;

                $fts += $ftpIndividual * $share;

                $totalConvertedPremium = ($doc->roe_fs > 0)
                    ? ($fts / $doc->roe_fs)
                    : 0;
            }

            if (!isset($grouped[$year])) {
                $grouped[$year] = [
                    'ftp' => 0,
                    'fts' => 0,
                ];
            }

            $grouped[$year]['ftp'] += $ftp;
            $grouped[$year]['fts'] += $totalConvertedPremium;

        }

            ksort($grouped);

            return [
            'labels' => array_keys($grouped),
            'ftp'    => array_values(array_column($grouped, 'ftp')),
            'fts'    => array_values(array_column($grouped, 'fts')),
        ];

    }

    public function mensualFtpFtsPorReinsurers(array $reinsurerIds, int $year)
    {
        if (empty($reinsurerIds)) {
            // Evitar consulta vacía que devuelve nada
            return collect();
        }

        return DB::table('operative_docs as od')
            ->join('businesses as b', 'b.business_code', '=', 'od.business_code')
            ->join('reinsurers as r', 'r.id', '=', 'b.reinsurer_id')
            ->join('businessdoc_insureds as i', 'i.op_document_id', '=', 'od.id')
            ->leftJoin('businessdoc_schemes as s', 's.op_document_id', '=', 'od.id')
            ->leftJoin('cost_schemes as cs', 'cs.id', '=', 's.cscheme_id')
            ->whereRaw('EXTRACT(YEAR FROM od.rep_date) = ?', [$year])
            ->whereIn('r.id', $reinsurerIds)
            ->selectRaw("
                r.id AS reinsurer_id,
                r.name AS reinsurer_name,
                DATE_TRUNC('month', od.rep_date) AS month_date,
                TO_CHAR(DATE_TRUNC('month', od.rep_date), 'Mon') AS month_label,

                -- Cálculo FTP exacto
                SUM(
                    (i.premium / 
                        CASE WHEN EXTRACT(YEAR FROM od.inception_date) % 4 = 0 THEN 366 ELSE 365 END
                    ) * (od.expiration_date::date - od.inception_date::date)
                ) AS ftp,

                -- Cálculo FTS exacto
                SUM(
                    (
                        (i.premium / 
                            CASE WHEN EXTRACT(YEAR FROM od.inception_date) % 4 = 0 THEN 366 ELSE 365 END
                        ) * (od.expiration_date::date - od.inception_date::date)
                    ) * COALESCE(cs.share, 0)
                    / NULLIF(od.roe_fs,0)
                ) AS fts
            ")
            ->groupBy('r.id', 'r.name', DB::raw("DATE_TRUNC('month', od.rep_date)"))
            ->orderBy(DB::raw("DATE_TRUNC('month', od.rep_date)"))
            ->get();
    }

    public function topReinsurersByYear(int $year, int $limit = 5)
    {
        return DB::table('operative_docs as od')
            ->join('businesses as b', 'b.business_code', '=', 'od.business_code')
            ->join('reinsurers as r', 'r.id', '=', 'b.reinsurer_id')
            ->join('businessdoc_insureds as i', 'i.op_document_id', '=', 'od.id')
            ->whereRaw('EXTRACT(YEAR FROM od.rep_date) = ?', [$year])
            ->selectRaw("r.id, r.name, SUM(i.premium) as total")
            ->groupBy('r.id', 'r.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}
