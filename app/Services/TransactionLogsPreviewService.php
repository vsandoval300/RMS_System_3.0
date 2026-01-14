<?php

namespace App\Services;

use App\Models\OperativeDoc;
use Illuminate\Support\Facades\DB;

class TransactionLogsPreviewService
{
    public function build(
        string $opDocumentId,
        int $typeId,
        float $proportion, // esperado en decimal: 0–1 (ej: 0.25)
        float $exchRate,
        ?string $remittanceCode = null,
        $dueDate = null,
    ): array {

        $doc = OperativeDoc::query()
            ->whereKey($opDocumentId)
            ->with([
                'schemes' => fn ($q) => $q->orderBy('index'),
                'schemes.costScheme' => fn ($q) => $q->with([
                    'costNodexes' => fn ($q) => $q
                        ->with([
                            'partnerSource:id,short_name,acronym,name',
                            'partnerDestination:id,short_name,acronym,name',
                            'deduction:id,concept',
                        ])
                        ->orderBy('index'),
                ]),
            ])
            ->first();

        if (! $doc) {
            return [];
        }

        /**
         * ✅ 1 query: suma de premiums por cscheme_id
         */
        $premiumSumBySchemeId = DB::table('businessdoc_insureds')
            ->where('op_document_id', $opDocumentId)
            ->whereNull('deleted_at')
            ->groupBy('cscheme_id')
            ->selectRaw('cscheme_id, COALESCE(SUM(premium), 0) as total_premium')
            ->pluck('total_premium', 'cscheme_id');

        // ⚠️ evitar división entre cero (se usa para TODO el documento)
        $safeExchRate = $exchRate > 0 ? $exchRate : 1;

        /**
         * ✅ Pre-calcular por cada scheme:
         *   - grossScheme (fijo por scheme)
         *   - nodes ordenados (para tomar el 1er, 2do, 3er nodo, etc.)
         */
        $schemeData = [];
        $maxSteps = 0;

        foreach ($doc->schemes as $schemeRow) {
            $scheme = $schemeRow->costScheme;

            if (! $scheme) {
                continue;
            }

            $nodes = $scheme->costNodexes ?? collect();
            $maxSteps = max($maxSteps, $nodes->count());

            $shareDecimal = $this->toDecimalRate($scheme->share ?? 0);
            $schemePremiumSum = (float) ($premiumSumBySchemeId[$scheme->id] ?? 0);

            // ✅ Gross por scheme (constante para ese scheme)
            $grossScheme = ($schemePremiumSum * $shareDecimal * $proportion) / $safeExchRate;

            $schemeData[] = [
                'scheme_id' => $scheme->id,
                'gross'     => (float) $grossScheme,
                'nodes'     => $nodes->values(), // reindex 0..n-1
            ];
        }

        if ($maxSteps === 0 || empty($schemeData)) {
            return [];
        }

        /**
         * ✅ totalGross = suma de grossScheme de todos los esquemas
         */
        $totalGrossAmount = round(collect($schemeData)->sum('gross'), 2);

        /**
         * ✅ Construir filas globales por "paso" (índice de nodo)
         */
        $rows = [];
        $bankingFee = 0.0;
        $prevNet = null;

        for ($step = 1; $step <= $maxSteps; $step++) {
            // gross del paso
            $grossAmount = ($step === 1)
                ? $totalGrossAmount
                : (float) $prevNet;

            // discount del paso = Σ (grossScheme * valueDelNodo(step))
            $stepDiscount = 0.0;

            // nodo referencia (solo display / ids)
            $refNode = null;

            foreach ($schemeData as $sd) {
                $nodes = $sd['nodes'];

                $node = $nodes->get($step - 1);
                if (! $node) {
                    continue;
                }

                if (! $refNode) {
                    $refNode = $node;
                }

                $valueDecimal = $this->toDecimalRate($node->value ?? 0);
                $stepDiscount += ((float) $sd['gross'] * (float) $valueDecimal);
            }

            $stepDiscount = round($stepDiscount, 2);

            // net = gross - discount - banking_fee
            $netAmount = round($grossAmount - $stepDiscount - $bankingFee, 2);
            $prevNet = $netAmount;

            $rows[] = [
                'index' => $step,

                'proportion'    => (float) $proportion,
                'exchange_rate' => (string) $exchRate,
                'exch_rate'     => (string) $exchRate,

                // ✅ Nuevo: % del nodo (guardar/display sin perder decimales)
                // Si el usuario capturó 12.345678, aquí debe verse igual
                'commission_percentage' => (string) $this->toDecimalRate($refNode?->value ?? 0),

                'deduction_id' => $refNode?->deduction?->id,
                'from_entity'  => $refNode?->partner_source_id ?? $refNode?->partnerSource?->id,
                'to_entity'    => $refNode?->partner_destination_id ?? $refNode?->partnerDestination?->id,

                'concept'     => $refNode?->deduction?->concept ?? '—',
                'source'      => $this->partnerLabel($refNode?->partnerSource),
                'destination' => $this->partnerLabel($refNode?->partnerDestination),

                'gross_amount' => $grossAmount,
                'discount'     => $stepDiscount,
                'banking_fee'  => 0,
                'net_amount'   => $netAmount,
            ];

        }

        return $rows;
    }

    /**
     * UI: 0.2 => 20, 20 => 20
     */
    private function toPercent(float|int|string $value): float
    {
        $v = (float) $value;

        return ($v > 0 && $v <= 1)
            ? round($v * 100, 2)
            : round($v, 2);
    }

    /**
     * Cálculo: 0.2 => 0.2, 20 => 0.2
     */
    private function toDecimalRate(float|int|string $value): float
    {
        $v = (float) $value;

        return $v > 1 ? $v / 100 : $v;
    }

    private function partnerLabel($partner): string
    {
        if (! $partner) {
            return '—';
        }

        $name = $partner->short_name ?? $partner->name ?? '—';
        $tag  = $partner->acronym ?? null;

        return $tag ? "{$name} - [{$tag}]" : $name;
    }
}
