<?php

namespace App\Services;

use App\Models\Deduction;
use App\Models\OperativeDoc;
use App\Models\TransactionLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CostScheme;

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
                    ->delete();
            } else {
                TransactionLog::query()
                    ->whereHas('transaction', function ($q) use ($docId) {
                        $q->withTrashed()->where('op_document_id', $docId);
                    })
                    ->whereNotIn('transaction_id', $validTxnIds)
                    ->delete();
            }

            $affected = 0;

            // 4) Rebuild por cada transacción
            foreach ($doc->transactions as $txn) {

                // [ADD] Normaliza proportion: 50 => 0.5 | 0.5 => 0.5
                $pRaw = (float) ($txn->proportion ?? 0);                // [ADD]
                $prop = $pRaw > 1 ? $pRaw / 100 : $pRaw;                // [ADD]

                // Base fija para descuentos
                $discountBase = $grandTotal;

                // [CHG] El gross escalado del primer renglón arranca en grandTotal * proportion
                $gScaled = round($grandTotal * $prop, 2);               // [CHG]

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

                    // [CHG] Descuento escalado por proportion del installment
                    $commissionScaled = round($discountBase * $pct * $prop, 2);   // [CHG]

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

                    // [CHG] Escala banking_fee existente por proportion
                    $bankingExisting = (float) ($log->banking_fee ?? 0.0);        // [ADD]
                    $bankingScaled   = round($bankingExisting * $prop, 2);        // [ADD]

                    // [CHG] Neto escalado
                    $netScaled = round($gScaled - $commissionScaled - $bankingScaled, 2); // [CHG]

                    // Campos calculados / de relación
                    $log->deduction_type = $deductionId;
                    $log->from_entity    = $node->partner_source_id ?? $log->from_entity;
                    $log->to_entity      = $node->partner_destination_id ?? $log->to_entity;
                    $log->exch_rate      = (float) ($txn->exch_rate ?? 1);

                    // [CHG] Guardar importes escalados
                    $log->gross_amount        = $gScaled;               // [CHG]
                    $log->commission_discount = $commissionScaled;      // [CHG]
                    $log->banking_fee         = $bankingScaled;         // [CHG]

                    // [CHG] Si net_amount ES columna generada, NO asignar esta línea:
                    // $log->net_amount = $netScaled;                    // [CHG] <-- comenta si es columna generada

                    $log->save();
                    $affected++;

                    // [CHG] El siguiente renglón parte del neto escalado actual
                    $gScaled = $netScaled;                               // [CHG]
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

    /**
     * Calcula filas "preview" sin tocar BD, usando el estado actual del formulario.
     * $form:
     *  - transactions: [ ['index'=>2,'proportion'=>0.5|50,'exch_rate'=>1.0,'due_date'=>'...'], ... ]
     *  - schemes:      [ ids de CostScheme seleccionados ] (opcional; si no, usa los del doc)
     *  - insureds:     estado del repeater insureds (opcional; si no, usa los del doc)
     */
    public function previewForOperativeDocState(string $docId, array $form): Collection
    {
        $doc = OperativeDoc::with([
            'insureds:id,op_document_id,premium',
            'schemes.costScheme.costNodexes' => fn ($q) => $q->orderBy('index'),
            'schemes.costScheme.costNodexes.deduction',
            'schemes.costScheme.costNodexes.partnerSource',
            'schemes.costScheme.costNodexes.partnerDestination',
        ])->findOrFail($docId);

        // --- Prima total
        $grandTotal = null;
        if (!empty($form['insureds'])) {
            $grandTotal = collect($form['insureds'])->sum(function ($i) {
                $raw = $i['premium'] ?? 0;
                $clean = is_string($raw) ? preg_replace('/[^0-9.]/', '', $raw) : $raw;
                if (is_string($clean)) {
                    $parts = explode('.', $clean, 3);
                    $clean = isset($parts[1]) ? $parts[0] . '.' . $parts[1] : $parts[0];
                }
                return (float) $clean;
            });
        } else {
            $grandTotal = (float) $doc->insureds->sum('premium');
        }

        // --- Nodos
        $nodes = collect();
        if (!empty($form['schemes'])) {
            $nodes = CostScheme::with([
                    'costNodexes' => fn ($q) => $q->orderBy('index'),
                    'costNodexes.deduction',
                    'costNodexes.partnerSource',
                    'costNodexes.partnerDestination',
                ])
                ->whereIn('id', $form['schemes'])
                ->get()
                ->flatMap(fn ($s) => $s->costNodexes ?? collect())
                ->sortBy('index')
                ->values();
        } else {
            $nodes = $doc->schemes
                ->flatMap(fn ($s) => $s->costScheme?->costNodexes ?? collect())
                ->sortBy('index')
                ->values();
        }

        // --- Normalizar transactions del form
        $txns = collect($form['transactions'] ?? [])
            ->map(function ($t) {
                $prop = $t['proportion'] ?? 0;
                $prop = is_string($prop) ? floatval(str_replace(',', '', $prop)) : (float) $prop;
                if ($prop > 1) $prop = $prop / 100;

                return [
                    'index'      => (int)($t['index'] ?? 0),
                    'proportion' => $prop,
                    'exch_rate'  => isset($t['exch_rate']) ? (float) $t['exch_rate'] : 1.0,
                    'due_date'   => $t['due_date'] ?? null,
                ];
            })
            ->sortBy('index')
            ->values();

        $discountBase = $grandTotal;

        $rows = collect();
        foreach ($txns as $txn) {
            // [ADD] proportion normalizada del installment
            $prop = (float) $txn['proportion'];                          // [ADD]

            // [CHG] gross escalado inicial
            $gScaled = round($grandTotal * $prop, 2);                    // [CHG]
            $i = 1;

            foreach ($nodes as $node) {
                $pctRaw = (float) ($node->value ?? 0);
                $pct    = $pctRaw > 1 ? $pctRaw / 100 : $pctRaw;

                // [CHG] descuento escalado
                $commissionScaled = round($discountBase * $pct * $prop, 2); // [CHG]

                // En preview banking fee = 0, pero mantenemos coherencia
                $bankingScaled = 0.0;                                     // [CHG]

                // [CHG] neto escalado
                $netScaled = round($gScaled - $commissionScaled - $bankingScaled, 2); // [CHG]

                $rows->push([
                    'inst_index'  => (int) $txn['index'],
                    'index'       => (int) ($node->index ?? $i),
                    'deduction'   => $node->deduction?->concept ?? '-',
                    'from'        => $node->partnerSource->short_name
                                        ?? $node->partnerSource->name
                                        ?? '—',
                    'to'          => optional($node->partnerDestination)->short_name
                                        ?? optional($node->partnerDestination)->name
                                        ?? '—',
                    'exch_rate'   => (float) $txn['exch_rate'],
                    'gross'       => $gScaled,            // [CHG]
                    'discount'    => $commissionScaled,   // [CHG]
                    'banking_fee' => $bankingScaled,      // [CHG]
                    'net'         => $netScaled,          // [CHG]
                ]);

                // [CHG] Siguiente renglón parte del neto escalado
                $gScaled = $netScaled;                                   // [CHG]
                $i++;
            }
        }

        return $rows->sortBy([['inst_index','asc'],['index','asc']])->values();
    }
}
