<?php

namespace App\Services;

use App\Models\Deduction;
use App\Models\OperativeDoc;
use App\Models\TransactionLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionLogBuilder
{
    /**
     * Reconstruye / actualiza transaction_logs de un OperativeDoc.
     * - Elimina logs huérfanos (de installments eliminados).
     * - Por cada transacción vigente, crea/actualiza un log por nodo de costo.
     * - Preserva campos operativos si el log ya existía (sent/received/banking_fee/status).
     *
     * @return int  Número de logs creados/actualizados/eliminados (aprox).
     */
    public function rebuildForOperativeDoc(string $docId): int
    {
        return DB::transaction(function () use ($docId) {
            /** @var OperativeDoc $doc */
            $doc = OperativeDoc::query()
                ->with([
                    'insureds:id,op_document_id,premium',
                    'transactions' => fn ($q) => $q->orderBy('index'),
                    'schemes.costScheme.costNodexes' => fn ($q) => $q->orderBy('index'),
                    // si tienes relación en el nodo:
                    'schemes.costScheme.costNodexes.deduction',
                ])
                ->findOrFail($docId);

            // 1) Prima total
            $grandTotal = (float) $doc->insureds->sum('premium');

            // 2) Nodos (en orden)
            /** @var Collection $nodes */
            $nodes = $doc->schemes
                ->flatMap(fn ($s) => $s->costScheme?->costNodexes ?? collect())
                ->sortBy('index')
                ->values();

            // 3) IDs de transacciones vigentes
            $validTxnIds = $doc->transactions->pluck('id')->all();

            if (empty($validTxnIds)) {
                TransactionLog::query()
                    ->whereHas('transaction', function ($q) use ($docId) {
                        $q->withTrashed()->where('op_document_id', $docId);
                    })
                    ->delete(); // soft-delete en logs
            } else {
                TransactionLog::query()
                    ->whereHas('transaction', function ($q) use ($docId) {
                        $q->withTrashed()->where('op_document_id', $docId);
                    })
                    ->whereNotIn('transaction_id', $validTxnIds)
                    ->delete(); // soft-delete en logs
            }
            // 3.b) LIMPIEZA: logs de transacciones que ya no existen
           /*  if (empty($validTxnIds)) {
                TransactionLog::query()
                    ->whereHas('transaction', fn ($q) => $q->where('op_document_id', $docId))
                    ->delete();
            } else {
                TransactionLog::query()
                    ->whereHas('transaction', fn ($q) => $q->where('op_document_id', $docId))
                    ->whereNotIn('transaction_id', $validTxnIds)
                    ->delete();
            } */

            $affected = 0;

            // 4) Rebuild por cada transacción
            foreach ($doc->transactions as $txn) {
                // proportion ya NO se usa para la base del descuento (según requerimiento)
                // pero la dejamos por si te interesa mostrarla en otro lado
                $installmentPremium = round($grandTotal * (float) ($txn->proportion ?? 0), 2);

                // ⬅️ NUEVO: la base para calcular TODOS los descuentos de este doc
                $discountBase = $grandTotal;

                // ⬅️ NUEVO: el gross del primer renglón arranca en el grand total
                $gross = round($grandTotal, 2);

                // Logs existentes por índice (para preservar campos operativos)
                $existing = TransactionLog::query()
                    ->where('transaction_id', $txn->id)
                    ->get()
                    ->keyBy('index');

                $i = 1;
                foreach ($nodes as $node) {
                    // Porcentaje robusto: acepta 3.5 (=> 0.035) o 0.035 directamente
                    $pctRaw = (float) ($node->value ?? 0);
                    $pct    = $pctRaw > 1 ? $pctRaw / 100 : $pctRaw;

                    // ⬅️ NUEVO: descuento SIEMPRE con base en el grand total
                    $commission = round($discountBase * $pct, 2);

                    // ✅ Asegurar un deduction_id NO nulo (igual que antes)
                    $deductionId =
                        ($node->deduction_id ?? null)
                        ?? ($node->deduction_type_id ?? null)
                        ?? ($node->deduction->id ?? null)
                        ?? config('rms.defaults.deduction_id')
                        ?? Deduction::query()->value('id');

                    if (! $deductionId) {
                        throw new \RuntimeException('No deduction_id available for cost node. Configure a default or ensure nodes carry a deduction.');
                    }

                    // Preserva sent/received/banking_fee/status si ya existe
                    $log = TransactionLog::firstOrNew([
                        'transaction_id' => $txn->id,
                        'index'          => $i,
                    ]);

                    if (! $log->exists) {
                        $log->id            = (string) Str::uuid();
                        $log->status        = 'Pending';
                        $log->sent_date     = null;
                        $log->received_date = null;
                        $log->banking_fee   = 0;
                    }

                    $bankingFee = (float) ($log->banking_fee ?? 0.0);

                    // ⬅️ NUEVO: neto descuenta el "commission" calculado con base fija + banking_fee
                    $net = round($gross - $commission - $bankingFee, 2);

                    // Campos calculados / de relación (igual que antes)
                    $log->deduction_type      = $deductionId;
                    $log->from_entity         = $node->partner_source_id ?? $log->from_entity;
                    $log->to_entity           = $node->partner_destination_id ?? $log->to_entity;
                    $log->exch_rate           = (float) ($txn->exch_rate ?? 1);
                    $log->gross_amount        = $gross;        // muestra el gross "que va quedando"
                    $log->commission_discount = $commission;   // SIEMPRE calculado con grand total
                    $log->net_amount          = $net;

                    $log->save();
                    $affected++;

                    // El siguiente renglón parte del neto del actual
                    $gross = $net;
                    $i++;
                }

                // 5) Poda interna: si ahora hay menos nodos, borra los sobrantes
                $affected += TransactionLog::query()
                    ->where('transaction_id', $txn->id)
                    ->where('index', '>=', $i)
                    ->delete();
            }

            return $affected;
        });
    }
}

