<?php

namespace App\Services;

use App\Models\Business;

class BusinessTechnicalResultService
{
    public function __construct(
        private readonly OperativeDocSummaryV2Service $docSummary,
    ) {}

    public function build(Business $business): array
    {
        $business->loadMissing(['reinsurer', 'currency', 'producer']);

        $docs = $business->operativeDocs()
            ->withoutTrashed()
            ->orderBy('index')
            ->get();

        $summaries       = [];
        $totalFts        = 0.0;
        $totalFtp        = 0.0;
        $totalDeductions = 0.0;

        foreach ($docs as $doc) {
            $summary          = $this->docSummary->build($doc->id);
            $totalFts        += (float) ($summary['totalPremiumFts']    ?? 0);
            $totalFtp        += (float) ($summary['totalPremiumFtp']    ?? 0);
            $totalDeductions += (float) ($summary['totalDeductionOrig'] ?? 0);
            $summaries[]      = $summary;
        }

        return [
            'business'        => $business,
            'generatedAt'     => now(),
            'summaries'       => $summaries,
            'totalFts'        => $totalFts,
            'totalFtp'        => $totalFtp,
            'totalDeductions' => $totalDeductions,
            'totalNet'        => $totalFts - $totalDeductions,
        ];
    }
}
