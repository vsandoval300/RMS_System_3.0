<?php

namespace App\Services;

use App\Models\OperativeDoc;
use App\Models\CostScheme;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PremiumForPeriodService
{
    public function anualFTS($reinsurerId): array
    {
        $query = OperativeDoc::query()
            ->whereYear('inception_date', '>=', 2010)
            ->with([
                'docType',
                'business.currency',
                'schemes.costScheme.costNodexes.partnerSource',
                'schemes.costScheme.costNodexes.deduction',
                'insureds.company.country',
                'insureds.coverage',
                'transactions.logs.toPartner',
            ]);

            if ($reinsurerId !== 'all') {
                $query->whereHas('business', function ($q) use ($reinsurerId) {
                    $q->where('reinsurer_id', $reinsurerId);
                });
            }

        $docs = $query->get();

        $grouped = [];

        foreach ($docs as $doc) {

            $year = \Carbon\Carbon::parse($doc->inception_date)->year;

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
            }

            if (!isset($grouped[$year])) {
                $grouped[$year] = [
                    'ftp' => 0,
                    'fts' => 0,
                ];
            }

            $grouped[$year]['ftp'] += $ftp;
            $grouped[$year]['fts'] += $fts;

        }

            ksort($grouped);

            return [
            'labels' => array_keys($grouped),
            'ftp'    => array_values(array_column($grouped, 'ftp')),
            'fts'    => array_values(array_column($grouped, 'fts')),
        ];

    }
}
