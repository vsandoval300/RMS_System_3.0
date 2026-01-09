<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionLogsPreviewService;
use App\Models\TransactionLog;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    /** @var array<string, mixed> */
    protected array $originalDrivers = [];

    protected function getRecordQuery(): Builder
    {
        return parent::getRecordQuery()->withTrashed();
    }

    /**
     * ✅ Snapshot al abrir el Edit (más confiable que beforeSave()).
     */
    public function mount($record): void
    {
        parent::mount($record);

        $this->originalDrivers = $this->record->only([
            'op_document_id',
            'transaction_type_id',
            'proportion',
            'exch_rate',
            'due_date',
            'remmitance_code',
        ]);
    }

    /**
     * ✅ Después de guardar, si cambiaron drivers, “reconstruye” logs.
     */
    protected function afterSave(): void
    {
        $transaction = $this->record->fresh();

        $newDrivers = $transaction->only([
            'op_document_id',
            'transaction_type_id',
            'proportion',
            'exch_rate',
            'due_date',
            'remmitance_code',
        ]);

        if (! $this->driversChanged($this->originalDrivers, $newDrivers)) {
            return;
        }

        DB::transaction(function () use ($transaction) {

            $rows = app(TransactionLogsPreviewService::class)->build(
                opDocumentId: (string) $transaction->op_document_id,
                typeId: (int) $transaction->transaction_type_id,
                proportion: (float) $transaction->proportion,
                exchRate: (float) $transaction->exch_rate,
                remittanceCode: $transaction->remmitance_code,
                dueDate: $transaction->due_date,
            );

            // Si ya no hay rows, borra (soft delete) todos los logs
            if (empty($rows)) {
                $transaction->logs()->delete();
                return;
            }

            // Indexar existentes por key estable
            $existing = $transaction->logs()
                ->get()
                ->keyBy(fn (TransactionLog $log) => $this->logKey([
                    'index'        => $log->index,
                    'deduction_id' => $log->deduction_type,
                    'from_entity'  => $log->from_entity,
                    'to_entity'    => $log->to_entity,
                ]));

            $touchedKeys = [];

            foreach ($rows as $row) {
                $key = $this->logKey($row);
                $touchedKeys[] = $key;

                /** @var TransactionLog|null $old */
                $old = $existing->get($key);

                // ✅ Derivado (lo que depende de Transaction + estructura CostNodes)
                $payload = [
                    'transaction_id' => $transaction->id,
                    'index'          => $row['index'] ?? null,

                    // FK -> deductions.id (BIGINT)
                    'deduction_type' => $row['deduction_id'] ?? null,

                    // FK -> partners.id (BIGINT)
                    'from_entity'    => $row['from_entity'] ?? null,
                    'to_entity'      => $row['to_entity'] ?? null,

                    // suele depender del header
                    'exch_rate'      => $row['exch_rate'] ?? (float) $transaction->exch_rate,
                ];

                if ($old) {
                    // ✅ Preserva operación / dinero ya capturado por el usuario
                    $payload['sent_date']           = $old->sent_date;
                    $payload['received_date']       = $old->received_date;
                    $payload['status']              = $old->status;
                    $payload['evidence_path']       = $old->evidence_path;

                    $payload['gross_amount']        = $old->gross_amount;
                    $payload['commission_discount'] = $old->commission_discount;
                    $payload['banking_fee']         = $old->banking_fee;

                    $old->fill($payload)->save();
                } else {
                    TransactionLog::create($payload + [
                        'gross_amount'        => 0,
                        'commission_discount' => 0,
                        'banking_fee'         => 0,
                    ]);
                }
            }

            // Soft-delete los que ya no existen en la nueva estructura
            $existing->each(function (TransactionLog $log, string $key) use ($touchedKeys) {
                if (! in_array($key, $touchedKeys, true)) {
                    $log->delete();
                }
            });
        });

        // ✅ actualiza snapshot para no re-disparar si el usuario guarda otra vez sin cambiar drivers
        $this->originalDrivers = $transaction->only([
            'op_document_id',
            'transaction_type_id',
            'proportion',
            'exch_rate',
            'due_date',
            'remmitance_code',
        ]);
    }

    private function driversChanged(array $old, array $new): bool
    {
        foreach ($old as $k => $v) {
            $a = is_string($v) ? trim($v) : $v;
            $b = is_string($new[$k] ?? null) ? trim((string) ($new[$k] ?? '')) : ($new[$k] ?? null);

            if ((string) $a !== (string) $b) {
                return true;
            }
        }
        return false;
    }

    private function logKey(array $row): string
    {
        return implode('|', [
            (string) ($row['index'] ?? ''),
            (string) ($row['deduction_id'] ?? $row['deduction_type'] ?? ''),
            (string) ($row['from_entity'] ?? ''),
            (string) ($row['to_entity'] ?? ''),
        ]);
    }

    // === Tu UI/acciones las puedes dejar como las tienes ===

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public function getTitle(): string
    {
        return 'Edit – ' . ($this->record?->name ?? 'Transaction');
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\RestoreAction::make()
                ->visible(fn () => $this->record->trashed()),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => $this->record->trashed()),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->requiresConfirmation()
            ->modalHeading('Save Transaction')
            ->modalDescription('Are you sure you want to save these changes?')
            ->modalSubmitActionLabel('Save')
            ->action(fn () => $this->save())
            ->keyBindings(['mod+s']);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction()
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
