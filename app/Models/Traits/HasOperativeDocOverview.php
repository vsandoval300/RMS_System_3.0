<?php

namespace App\Models\Traits;

use Filament\Forms\Components\View;
use Filament\Forms\Get;

trait HasOperativeDocOverview
{
    /**
     * Vista reutilizable del Overview (para sección y para modal).
     */
    public static function makeOperativeDocOverviewView(): View
    {
        return View::make('filament.resources.business.operative-doc-summary')
            ->extraAttributes([
                'class' => 'bg-[#dfe0e2] text-black p-4 rounded-md',
            ])
            ->reactive()
            ->viewData(function (Get $get, $record, $livewire) {

                // 👇 Estado completo del formulario principal
                // (en RelationManager & Pages suele estar en $livewire->data)
                $state = $livewire->data ?? [];

                // Preferimos el estado global, y si no existe, usamos el $get local
                $schemesState      = $state['schemes']      ?? $get('schemes')      ?? [];
                $insuredsState     = $state['insureds']     ?? $get('insureds')     ?? [];
                $transactionsState = $state['transactions'] ?? $get('transactions') ?? [];
                $inception         = $state['inception_date']  ?? $get('inception_date');
                $expiration        = $state['expiration_date'] ?? $get('expiration_date');

                $business = method_exists($livewire, 'getOwnerRecord')
                    ? $livewire->getOwnerRecord()
                    : null;

                // 🔸 Schemes con datos relevantes ya cargados
                $schemes = collect($schemesState)
                    ->map(function ($scheme) {
                        $model = \App\Models\CostScheme::find($scheme['cscheme_id'] ?? null);
                        return $model ? [
                            'id'             => $model->id,
                            'share'          => $model->share,
                            'agreement_type' => $model->agreement_type,
                        ] : null;
                    })
                    ->filter()
                    ->values()
                    ->toArray();

                $totalShare = collect($schemes)->sum('share');

                // 🔹 Insureds con limpieza de premium
                $insureds = collect($insuredsState)->map(function ($insured) {
                    $company  = \App\Models\Company::with('country')->find($insured['company_id'] ?? null);
                    $coverage = \App\Models\Coverage::find($insured['coverage_id'] ?? null);

                    $raw   = $insured['premium'] ?? 0;
                    $clean = is_string($raw) ? preg_replace('/[^0-9.]/', '', $raw) : $raw;
                    if (is_string($clean)) {
                        $parts = explode('.', $clean, 3);
                        $clean = isset($parts[1]) ? $parts[0] . '.' . $parts[1] : $parts[0];
                    }
                    $premium = floatval($clean);

                    return [
                        'company' => $company
                            ? [
                                'name'    => $company->name,
                                'country' => ['name' => optional($company->country)->name],
                            ]
                            : ['name' => '-', 'country' => ['name' => '-']],
                        'coverage' => $coverage
                            ? ['name' => $coverage->name]
                            : ['name' => '-'],
                        'premium'  => $premium,
                    ];
                })->toArray();

                // 🔹 Cost nodes
                $costNodes = collect($schemesState)
                    ->map(fn ($scheme) =>
                        \App\Models\CostScheme::with(
                            'costNodexes.costScheme',
                            'costNodexes.partnerSource',
                            'costNodexes.deduction'
                        )->find($scheme['cscheme_id'] ?? null)
                    )
                    ->filter()
                    ->flatMap(fn ($scheme) => $scheme->costNodexes ?? collect())
                    ->values();

                // 📊 Cálculos generales
                $start        = $inception  ? \Carbon\Carbon::parse($inception)  : null;
                $end          = $expiration ? \Carbon\Carbon::parse($expiration) : null;
                $coverageDays = ($start && $end) ? $start->diffInDays($end) : 0;
                $daysInYear   = $start && $start->isLeapYear() ? 366 : 365;

                $totalPremium = collect($insureds)->sum('premium');

                $insureds = collect($insureds)->map(
                    function ($insured) use ($totalPremium, $coverageDays, $daysInYear, $schemes) {
                        $allocation = $totalPremium > 0 ? $insured['premium'] / $totalPremium : 0;
                        $premiumFtp = ($daysInYear > 0)
                            ? ($insured['premium'] / $daysInYear) * $coverageDays
                            : 0;

                        $premiumFts = 0;
                        foreach ($schemes as $s) {
                            $premiumFts += $premiumFtp * ($s['share'] ?? 0);
                        }

                        return array_merge($insured, [
                            'allocation_percent' => $allocation,
                            'premium_ftp'        => $premiumFtp,
                            'premium_fts'        => $premiumFts,
                        ]);
                    }
                )->toArray();

                $totalPremiumFtp = ($daysInYear > 0)
                    ? ($totalPremium / $daysInYear) * $coverageDays
                    : 0;

                $totalPremiumFts = 0;
                foreach ($schemes as $s) {
                    $totalPremiumFts += $totalPremiumFtp * ($s['share'] ?? 0);
                }

                // Converted premium
                $transactions          = collect($transactionsState);
                $totalConvertedPremium = 0;

                foreach ($transactions as $txn) {
                    $proportion = floatval($txn['proportion'] ?? 0) / 100;
                    $rate       = floatval($txn['exch_rate'] ?? 0);

                    if ($rate > 0) {
                        $totalConvertedPremium += ($totalPremiumFts * $proportion) / $rate;
                    } else {
                        $totalConvertedPremium = 1;
                    }
                }

                $totalDeductionOrig = 0;
                $totalDeductionUsd  = 0;

                $groupedCostNodes = $costNodes
                    ->groupBy(fn ($node) => $node->costSchemes->share ?? 0)
                    ->map(function ($nodes, $share)
                        use (&$totalDeductionOrig, &$totalDeductionUsd,
                            $totalPremiumFts, $totalConvertedPremium) {

                        $shareFloat = floatval($share);

                        $nodeList = $nodes->map(function ($node)
                            use ($shareFloat, $totalPremiumFts, $totalConvertedPremium) {

                            $deduction          = $totalPremiumFts * $node->value * $shareFloat;
                            $deductionConverted = $totalConvertedPremium * $node->value * $shareFloat;

                            return [
                                'index'            => $node->index,
                                'partner'          => $node->partnerSource?->name ?? '-',
                                'partner_short'    => $node->partnerSource?->short_name
                                                      ?? ($node->partnerSource?->name ?? '-'),
                                'deduction'        => $node->deduction?->concept ?? '-',
                                'value'            => $node->value,
                                'share'            => $shareFloat,
                                'deduction_amount' => $deduction,
                                'deduction_usd'    => $deductionConverted,
                            ];
                        })->values();

                        $subtotalOrig = $nodeList->sum('deduction_amount');
                        $subtotalUsd  = $nodeList->sum('deduction_usd');

                        $totalDeductionOrig += $subtotalOrig;
                        $totalDeductionUsd  += $subtotalUsd;

                        return [
                            'share'         => $shareFloat,
                            'nodes'         => $nodeList,
                            'subtotal_orig' => $subtotalOrig,
                            'subtotal_usd'  => $subtotalUsd,
                        ];
                    })
                    ->sortKeys()
                    ->values()
                    ->toArray();

                // Logs por transacción
                $persistedTxIds = collect($transactionsState)
                    ->pluck('id')->filter()->values();

                $logsByTxn = [];

                if ($persistedTxIds->isNotEmpty()) {
                    $logs = \App\Models\TransactionLog::with('toPartner')
                        ->whereIn('transaction_id', $persistedTxIds)
                        ->get();

                    $logsByTxn = $logs->groupBy('transaction_id')->map(function ($grp) {
                        return $grp->mapWithKeys(function ($log) {
                            $idx = (int)($log->index ?? 0);
                            return [
                                $idx => [
                                    'to_short'  => $log->toPartner?->short_name
                                                   ?? $log->toPartner?->name
                                                   ?? '-',
                                    'to_full'   => $log->toPartner?->name ?? '-',
                                    'exch_rate' => $log->exch_rate,
                                    'gross'     => $log->gross_amount,
                                    'discount'  => $log->commission_discount,
                                    'banking'   => $log->banking_fee,
                                    'net'       => $log->net_amount,
                                    'status'    => $log->status,
                                ],
                            ];
                        });
                    })->toArray();
                }

                return [
                    'id'                   => $state['id'] ?? $get('id'),
                    'createdAt'            => $record?->created_at ?? now(),
                    'documentType'         => ($docTypeId = $state['operative_doc_type_id']
                        ?? $get('operative_doc_type_id'))
                        ? \App\Models\BusinessDocType::find($docTypeId)?->name ?? '-'
                        : '-',
                    'inceptionDate'        => $inception,
                    'expirationDate'       => $expiration,
                    'premiumType'          => $record?->business?->premium_type
                        ?? $business?->premium_type
                        ?? '-',
                    'originalCurrency'     => $record?->business?->currency?->acronym
                        ?? $business?->currency?->acronym
                        ?? '-',
                    'insureds'             => array_values($insureds),
                    'costSchemes'          => $schemes,
                    'groupedCostNodes'     => $groupedCostNodes,
                    'totalPremiumFts'      => $totalPremiumFts,
                    'totalPremiumFtp'      => $totalPremiumFtp,
                    'totalConvertedPremium'=> $totalConvertedPremium,
                    'coverageDays'         => $coverageDays,
                    'totalDeductionOrig'   => $totalDeductionOrig,
                    'totalDeductionUsd'    => $totalDeductionUsd,
                    'totalShare'           => $totalShare,
                    'transactions'         => collect($transactionsState)->values(),
                    'logsByTxn'            => $logsByTxn,
                ];
            });
    }
}
