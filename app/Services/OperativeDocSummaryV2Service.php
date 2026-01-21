<?php

namespace App\Services;

use App\Models\OperativeDoc;
use App\Models\CostScheme;
use App\Models\TransactionLog;
use Carbon\Carbon;

class OperativeDocSummaryV2Service
{
    public function build(string $opDocId): array
    {
        // ✅ AJUSTA relaciones si difieren en tu modelo
        $doc = OperativeDoc::query()
            ->whereKey($opDocId)
            ->with([
                'docType',               // tipo doc
                'business.currency',     // moneda original
                'schemes' => fn ($q) => $q->orderBy('index'),        // pivote businessdoc_schemes
                'schemes.costScheme' => fn ($q) => $q->with([
                    'costNodexes.partnerSource',
                    'costNodexes.deduction',
                ]),
                'insureds.company.country', // businessdoc_insureds -> company -> country
                'insureds.coverage',
                'transactions' => fn ($q) => $q->orderBy('index'),   // installments
                'transactions.logs' => fn ($q) => $q->orderBy('index'),
                'transactions.logs.toPartner', // logs + toPartner
            ])
            ->firstOrFail();

        /* ============================================================
        |  1) Datos base
        ============================================================ */
        $inception   = $doc->inception_date ? Carbon::parse($doc->inception_date) : null;
        $expiration  = $doc->expiration_date ? Carbon::parse($doc->expiration_date) : null;

        $daysInYear = $inception && $inception->isLeapYear() ? 366 : 365;

        $coverageDays = ($inception && $expiration) ? $inception->diffInDays($expiration) : 0;

        // regla anti-distorsión que ya tenías
        if ($inception && $expiration && $inception->isSameDay($expiration->copy()->subYear())) {
            $coverageDays = $daysInYear;
        }

        /* ============================================================
        |  2) Cost Schemes (Placement Schemes)
        |  Aquí asumimos que $doc->schemes es el pivote y trae costScheme
        ============================================================ */
        $costSchemes = collect($doc->schemes ?? [])
            ->map(function ($pivotRow) {
                $cs = $pivotRow->costScheme ?? null;
                if (! $cs) return null;

                return [
                    'cscheme_id'     => $cs->getKey(),          // id num/uuid interno
                    'id'             => $cs->id,                // SCHE-...
                    'description'    => $cs->description,
                    'share'          => (float) $cs->share,
                    'agreement_type' => $cs->agreement_type,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        $schemeShareById = collect($costSchemes)
            ->mapWithKeys(fn ($s) => [($s['cscheme_id'] ?? null) => (float) ($s['share'] ?? 0)])
            ->filter();

        $totalShare = collect($costSchemes)->sum('share');

        /* ============================================================
        |  3) Insureds
        |  Deben traer cscheme_id, premium, company/country, coverage
        |  AJUSTA si tu insured tiene el campo cscheme_id con otro nombre
        ============================================================ */
        $insureds = collect($doc->insureds ?? [])
            ->map(function ($insured) {
                $premium = (float) ($insured->premium ?? 0);

                return [
                    'cscheme_id' => $insured->cscheme_id ?? $insured->cost_scheme_id ?? null, // ✅ AJUSTA AQUÍ

                    'company' => [
                        'name' => $insured->company?->name ?? '-',
                        'country' => [
                            'name' => $insured->company?->country?->name ?? '-',
                        ],
                    ],
                    'coverage' => [
                        'name' => $insured->coverage?->name ?? '-',
                    ],
                    'premium'  => $premium,
                ];
            })
            ->values()
            ->toArray();

        $totalPremium = collect($insureds)->sum('premium');

        // FTP/FTS por insured (igual que en v1)
        $insureds = collect($insureds)->map(function ($insured) use ($totalPremium, $coverageDays, $daysInYear, $schemeShareById) {
            $allocation = $totalPremium > 0 ? $insured['premium'] / $totalPremium : 0;

            $premiumFtp = ($daysInYear > 0)
                ? ($insured['premium'] / $daysInYear) * $coverageDays
                : 0;

            $schemeId = $insured['cscheme_id'] ?? null;
            $share    = (float) ($schemeShareById[$schemeId] ?? 0);

            $premiumFts = $premiumFtp * $share;

            return array_merge($insured, [
                'allocation_percent' => $allocation,
                'premium_ftp'        => $premiumFtp,
                'premium_fts'        => $premiumFts,
                'scheme_share'       => $share,
            ]);
        })->values()->toArray();

        $totalPremiumFtp = ($daysInYear > 0) ? ($totalPremium / $daysInYear) * $coverageDays : 0;
        $totalPremiumFts = collect($insureds)->sum('premium_fts');

        /* ============================================================
        |  4) Transactions (Installments)
        ============================================================ */
        $transactions = collect($doc->transactions ?? [])
            ->map(fn ($t) => [
                'id'        => $t->id,
                'index'     => (int) ($t->index ?? 0),
                'proportion'=> (float) ($t->proportion ?? 0), // OJO: si guardas 0-1 vs 0-100
                'exch_rate' => (float) ($t->exch_rate ?? 0),
                'due_date'  => $t->due_date,
            ])
            ->values();

        // normaliza proportion: si viene 0.25 => 25, etc. (elige un estándar)
        // En tu v1 asumías que viene 0-100; aquí lo dejamos igual y corrige si guardas decimal.
        $totalConvertedPremium = 0.0;
        foreach ($transactions as $txn) {
            $pRaw = (float) ($txn['proportion'] ?? 0);
            $proportion = $pRaw > 1 ? $pRaw / 100 : $pRaw;
            $rate = (float) ($txn['exch_rate'] ?? 0);

            if ($rate > 0) {
                $totalConvertedPremium += ($totalPremiumFts * $proportion) / $rate;
            }
        }

        /* ============================================================
        |  5) Costs Breakdown: groupedCostNodes (igual fórmula v1)
        |  Base por scheme = sum(premium_fts) de insureds del scheme
        ============================================================ */
        $premiumFtsByScheme = collect($insureds)
            ->groupBy('cscheme_id')
            ->map(fn ($rows) => $rows->sum('premium_fts'));

        $convertedFtsByScheme = $premiumFtsByScheme->map(function ($schemeFts) use ($transactions) {
            $converted = 0.0;

            foreach ($transactions as $txn) {
                $pRaw = (float) ($txn['proportion'] ?? 0);
                $proportion = $pRaw > 1 ? $pRaw / 100 : $pRaw;
                $rate = (float) ($txn['exch_rate'] ?? 0);

                if ($rate > 0) {
                    $converted += ($schemeFts * $proportion) / $rate;
                }
            }

            return $converted;
        });

        // Flatten cost nodes from schemes
        $costNodes = collect($doc->schemes ?? [])
            ->flatMap(function ($pivotRow) {
                $cs = $pivotRow->costScheme;
                if (! $cs) return collect();

                return $cs->costNodexes->map(function ($node) use ($cs) {
                    $node->cscheme_id    = $cs->getKey();
                    $node->scheme_share  = (float) $cs->share;
                    return $node;
                });
            })
            ->values();

        $totalDeductionOrig = 0.0;
        $totalDeductionUsd  = 0.0;

        $groupedCostNodes = $costNodes
            ->groupBy('cscheme_id')
            ->map(function ($nodes, $schemeId) use (
                &$totalDeductionOrig,
                &$totalDeductionUsd,
                $premiumFtsByScheme,
                $convertedFtsByScheme
            ) {
                $first      = $nodes->first();
                $shareFloat = (float) ($first->scheme_share ?? 0);

                $schemeBaseOrig = (float) ($premiumFtsByScheme[$schemeId] ?? 0);
                $schemeBaseUsd  = (float) ($convertedFtsByScheme[$schemeId] ?? 0);

                $nodeList = $nodes->map(function ($node) use ($schemeBaseOrig, $schemeBaseUsd, $shareFloat) {
                    $rate = (float) ($node->value ?? 0);

                    $deductionOrig = $schemeBaseOrig * $rate;
                    $deductionUsd  = $schemeBaseUsd  * $rate;

                    return [
                        'index'            => $node->index,
                        'partner'          => $node->partnerSource?->name ?? '-',
                        'partner_short'    => $node->partnerSource?->short_name
                                            ?? $node->partnerSource?->name
                                            ?? '-',
                        'deduction'        => $node->deduction?->concept ?? '-',
                        'value'            => $rate,
                        'share'            => $shareFloat,

                        'scheme_base_orig' => $schemeBaseOrig,
                        'scheme_base_usd'  => $schemeBaseUsd,

                        'deduction_amount' => $deductionOrig,
                        'deduction_usd'    => $deductionUsd,
                    ];
                })->values();

                $subtotalOrig = $nodeList->sum('deduction_amount');
                $subtotalUsd  = $nodeList->sum('deduction_usd');

                $totalDeductionOrig += $subtotalOrig;
                $totalDeductionUsd  += $subtotalUsd;

                return [
                    'scheme_id'        => $schemeId,
                    'share'            => $shareFloat,
                    'scheme_base_orig' => $schemeBaseOrig,
                    'scheme_base_usd'  => $schemeBaseUsd,
                    'nodes'            => $nodeList,
                    'subtotal_orig'    => $subtotalOrig,
                    'subtotal_usd'     => $subtotalUsd,
                ];
            })
            ->values()
            ->toArray();

        /* ============================================================
        |  6) Logs by Txn (igual que v1 pero desde relaciones)
        ============================================================ */
        $logsByTxn = collect($doc->transactions ?? [])
            ->mapWithKeys(function ($txn) {
                $rows = collect($txn->logs ?? [])
                    ->sortBy('index') // ✅ importante
                    ->mapWithKeys(function ($log) {
                        $idx = (int) ($log->index ?? 0);

                        return [
                            $idx => [
                                'to_short'  => $log->toPartner?->short_name
                                            ?? $log->toPartner?->name
                                            ?? '-',
                                'to_full'   => $log->toPartner?->name ?? '-',

                                // ✅ usa el exch_rate del log si aplica
                                'exch_rate' => (float) ($log->exch_rate ?? 0),

                                // ✅ usa el “calc” (ya proporcional) si existe
                                'gross'     => $log->gross_amount_calc ?? $log->gross_amount,

                                'discount'  => $log->commission_discount,
                                'banking'   => $log->banking_fee,
                                'net'       => $log->net_amount,
                                'status'    => $log->status,
                            ],
                        ];
                    });

                return [$txn->id => $rows];
            })
            ->toArray();

        /* ============================================================
        |  RETURN final (lo que la vista espera)
        ============================================================ */
        return [
            'id' => $doc->id,
            'createdAt' => $doc->created_at,
            'documentType' => $doc->docType?->name ?? '-',
            'inceptionDate' => $doc->inception_date,
            'expirationDate' => $doc->expiration_date,
            'premiumType' => $doc->business?->premium_type ?? '-',
            'originalCurrency' => $doc->business?->currency?->acronym ?? '-',

            'insureds' => $insureds,
            'costSchemes' => $costSchemes,
            'groupedCostNodes' => $groupedCostNodes,

            'totalPremiumFts' => $totalPremiumFts,
            'totalPremiumFtp' => $totalPremiumFtp,
            'totalConvertedPremium' => $totalConvertedPremium,

            'premiumFtsByScheme' => $premiumFtsByScheme,
            'convertedFtsByScheme' => $convertedFtsByScheme,

            'coverageDays' => $coverageDays,
            'totalDeductionOrig' => $totalDeductionOrig,
            'totalDeductionUsd' => $totalDeductionUsd,
            'totalShare' => $totalShare,

            'transactions' => $transactions->toArray(),
            'logsByTxn' => $logsByTxn,
        ];
    }
}
