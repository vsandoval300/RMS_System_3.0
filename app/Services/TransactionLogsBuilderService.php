<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionLogsBuilderService
{
    public function preview(
        string $opDocumentId,
        int $typeId,
        float $proportion,
        float $exchRate,
        ?string $remittanceCode,
        mixed $dueDate,
    ): array {
        return app(TransactionLogsPreviewService::class)->build(
            opDocumentId: $opDocumentId,
            typeId: $typeId,
            proportion: $proportion,
            exchRate: $exchRate,
            remittanceCode: $remittanceCode,
            dueDate: $dueDate,
        );
    }

    public function payloadForInsert(Transaction $transaction): array
    {
        $rows = $this->preview(
            opDocumentId: (string) $transaction->op_document_id,
            typeId: (int) $transaction->transaction_type_id,
            proportion: (float) $transaction->proportion,
            exchRate: (float) $transaction->exch_rate,
            remittanceCode: $transaction->remmitance_code,
            dueDate: $transaction->due_date,
        );

        $now = now();

        return collect($rows)->map(function (array $row) use ($transaction, $now) {
            return [
                'id'             => (string) Str::uuid(),
                'transaction_id' => $transaction->id,
                'index'          => (int) ($row['index'] ?? 1),

                'deduction_type' => $row['deduction_id'] ?? $row['deduction_type'] ?? null,
                'from_entity'    => $row['from_entity'] ?? null,
                'to_entity'      => $row['to_entity'] ?? null,

                'sent_date'      => null,
                'received_date'  => null,

                'exch_rate'      => (string) ($row['exchange_rate'] ?? $transaction->exch_rate),
                'proportion'     => (string) ($row['proportion'] ?? $transaction->proportion),

                'commission_percentage' => (string) ($row['commission_percentage'] ?? 0),

                'gross_amount'   => (string) ($row['gross_amount'] ?? 0),
                'banking_fee'    => (string) ($row['banking_fee'] ?? 0),

                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        })->all();
    }
}