<?php

namespace App\Filament\Resources\Transactions\Pages;

use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionLogsPreviewService;
use App\Models\TransactionLog;
use App\Models\TransactionRecalculation;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    /** @var array<string, mixed> */
    protected array $originalDrivers = [];

    protected function getRecordQuery(): Builder
    {
        return parent::getRecordQuery()->withTrashed();
    }

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

    protected function afterSave(): void
    {
        $this->originalDrivers = $this->record->fresh()->only([
            'op_document_id',
            'transaction_type_id',
            'proportion',
            'exch_rate',
            'due_date',
            'remmitance_code',
        ]);
    }

    private function recalculateLifecycle(): void
    {
        $transaction = $this->record->fresh();

        DB::transaction(function () use ($transaction) {

            Log::info('=== RECALCULATE LIFECYCLE START ===', [
                'transaction_id' => $transaction->id,
                'proportion'     => $transaction->proportion,
                'exch_rate'      => $transaction->exch_rate,
                'type_id'        => $transaction->transaction_type_id,
                'document_id'    => $transaction->op_document_id,
            ]);

            $rows = app(TransactionLogsPreviewService::class)->build(
                opDocumentId: (string) $transaction->op_document_id,
                typeId: (int) $transaction->transaction_type_id,
                proportion: (float) $transaction->proportion,
                exchRate: (float) $transaction->exch_rate,
                remittanceCode: $transaction->remmitance_code,
                dueDate: $transaction->due_date,
            );

            Log::info('Preview rows generated', [
                'rows_count' => count($rows),
                'rows'       => $rows,
            ]);

            if (empty($rows)) {
                $transaction->logs()->delete();

                Log::warning('No rows generated. Existing logs deleted.', [
                    'transaction_id' => $transaction->id,
                ]);

                return;
            }

            $allLogs = $transaction->logs()->get();

            // Apply banking_fee cascade to service rows before saving.
            // The service always uses banking_fee=0 in its chain, so we correct each
            // row's gross_amount and net_amount using the actual banking_fees from the
            // existing logs, propagating: gross[N+1] = net[N].
            $rows = $this->applyBankingFeeCascade($rows, $allLogs->keyBy('index'));

            $existing = $allLogs
                ->keyBy(fn (TransactionLog $log) => $this->logKey([
                    'index'        => $log->index,
                    'deduction_id' => $log->deduction_type,
                    'from_entity'  => $log->from_entity,
                    'to_entity'    => $log->to_entity,
                ]));

            $touchedKeys = [];

            foreach ($rows as $row) {

                Log::info('Processing row', [
                    'row' => $row,
                ]);

                $key = $this->logKey($row);
                $touchedKeys[] = $key;

                /** @var TransactionLog|null $old */
                $old = $existing->get($key);

                $payload = [
                    'transaction_id' => $transaction->id,
                    'index'          => $row['index'] ?? null,

                    'deduction_type' => $row['deduction_id'] ?? null,
                    'from_entity'    => $row['from_entity'] ?? null,
                    'to_entity'      => $row['to_entity'] ?? null,

                    'proportion'     => $row['proportion'] ?? $transaction->proportion,
                    'exch_rate'      => $row['exchange_rate']
                        ?? $row['exch_rate']
                        ?? $transaction->exch_rate,

                    'commission_percentage' =>
                        $row['commission_percentage'] ?? 0,

                    'gross_amount' =>
                        $row['gross_amount'] ?? 0,

                    'gross_amount_calc' =>
                        $row['gross_amount'] ?? 0,

                    'commission_discount' =>
                        $row['discount']
                        ?? $row['commission_discount']
                        ?? 0,

                    'net_amount' =>
                        $row['net_amount'] ?? 0,
                ];

                Log::info('Payload generated', [
                    'payload' => $payload,
                ]);

                if ($old) {

                    $payload['sent_date']     = $old->sent_date;
                    $payload['received_date'] = $old->received_date;
                    $payload['status']        = $old->status;
                    $payload['evidence_path'] = $old->evidence_path;
                    $payload['banking_fee']   = $row['banking_fee'] ?? $old->banking_fee;

                    $old->fill($payload)->save();

                    Log::info('TransactionLog updated', [
                        'log_id' => $old->id,
                        'gross_amount' => $old->gross_amount,
                        'net_amount' => $old->net_amount,
                        'proportion' => $old->proportion,
                    ]);

                } else {

                    $payload['sent_date']     = null;
                    $payload['received_date'] = null;
                    $payload['status']        = 'Pending';
                    $payload['evidence_path'] = null;
                    $payload['banking_fee']   = $row['banking_fee'] ?? 0;

                    $newLog = TransactionLog::create($payload);

                    Log::info('TransactionLog created', [
                        'log_id' => $newLog->id,
                        'gross_amount' => $newLog->gross_amount,
                        'net_amount' => $newLog->net_amount,
                        'proportion' => $newLog->proportion,
                    ]);
                }
            }

            $existing->each(function (
                TransactionLog $log,
                string $key
            ) use ($touchedKeys) {

                if (! in_array($key, $touchedKeys, true)) {

                    Log::info('Deleting obsolete log', [
                        'log_id' => $log->id,
                    ]);

                    $log->delete();
                }
            });

            Log::info('=== RECALCULATE LIFECYCLE END ===');
        });

        Notification::make()
            ->success()
            ->title('Lifecycle recalculated')
            ->body('The transaction lifecycle was recalculated successfully.')
            ->send();
    }

    private function applyBordereaux(array $data): void
    {
        $transaction = $this->record->fresh();
        $newAmount   = (float) ($data['new_amount'] ?? 0);
        $prevAmount  = (float) ($transaction->amount ?? 0);

        DB::transaction(function () use ($transaction, $data, $newAmount, $prevAmount) {

            TransactionRecalculation::create([
                'transaction_id'       => $transaction->id,
                'bordereaux_reference' => $data['bordereaux_reference'] ?? null,
                'reported_premium'     => (float) ($data['reported_premium'] ?? 0),
                'reported_claims'      => (float) ($data['reported_claims'] ?? 0),
                'previous_amount'      => $prevAmount,
                'new_amount'           => $newAmount,
                'evidence_path'        => $data['evidence_path'] ?? null,
                'notes'                => $data['notes'] ?? null,
                'created_by'           => Auth::id(),
            ]);

            $transaction->amount = $newAmount;
            $transaction->saveQuietly();

            $rows = app(TransactionLogsPreviewService::class)->build(
                opDocumentId:        (string) $transaction->op_document_id,
                typeId:              (int) $transaction->transaction_type_id,
                proportion:          (float) $transaction->proportion,
                exchRate:            (float) $transaction->exch_rate,
                remittanceCode:      $transaction->remmitance_code,
                dueDate:             $transaction->due_date,
                overrideBasePremium: $newAmount,
            );

            if (empty($rows)) {
                return;
            }

            $allLogs = $transaction->logs()->get();

            // Correct the service's "clean" chain (banking_fee=0) with actual banking_fees.
            $rows = $this->applyBankingFeeCascade($rows, $allLogs->keyBy('index'));

            $existing = $allLogs
                ->keyBy(fn (TransactionLog $log) => $this->logKey([
                    'index'        => $log->index,
                    'deduction_id' => $log->deduction_type,
                    'from_entity'  => $log->from_entity,
                    'to_entity'    => $log->to_entity,
                ]));

            $touchedKeys = [];

            foreach ($rows as $row) {
                $key           = $this->logKey($row);
                $touchedKeys[] = $key;

                $old = $existing->get($key);

                $payload = [
                    'transaction_id'        => $transaction->id,
                    'index'                 => $row['index'] ?? null,
                    'deduction_type'        => $row['deduction_id'] ?? null,
                    'from_entity'           => $row['from_entity'] ?? null,
                    'to_entity'             => $row['to_entity'] ?? null,
                    'proportion'            => $row['proportion'] ?? $transaction->proportion,
                    'exch_rate'             => $row['exchange_rate'] ?? $row['exch_rate'] ?? $transaction->exch_rate,
                    'commission_percentage' => $row['commission_percentage'] ?? 0,
                    'gross_amount'          => $row['gross_amount'] ?? 0,
                    'gross_amount_calc'     => $row['gross_amount'] ?? 0,
                    'commission_discount'   => $row['discount'] ?? $row['commission_discount'] ?? 0,
                    'net_amount'            => $row['net_amount'] ?? 0,
                ];

                if ($old) {
                    $payload['sent_date']     = $old->sent_date;
                    $payload['received_date'] = $old->received_date;
                    $payload['status']        = $old->status;
                    $payload['evidence_path'] = $old->evidence_path;
                    $payload['banking_fee']   = $row['banking_fee'] ?? $old->banking_fee;

                    $old->fill($payload)->save();
                } else {
                    $payload['sent_date']     = null;
                    $payload['received_date'] = null;
                    $payload['status']        = 'Pending';
                    $payload['evidence_path'] = null;
                    $payload['banking_fee']   = $row['banking_fee'] ?? 0;

                    TransactionLog::create($payload);
                }
            }

            $existing->each(function (TransactionLog $log, string $key) use ($touchedKeys) {
                if (! in_array($key, $touchedKeys, true)) {
                    $log->delete();
                }
            });
        });

        Notification::make()
            ->success()
            ->title('Bordereaux applied')
            ->body('The transaction has been recalculated from the bordereaux amount.')
            ->send();
    }

    /**
     * The service computes a "clean" chain using banking_fee=0 at every step.
     * This method walks the rows in index order and:
     *   1. Replaces each gross_amount (for step > 1) with the previous step's net_amount.
     *   2. Restores the actual banking_fee from the existing logs.
     *   3. Recomputes net_amount = gross - discount - banking_fee.
     *
     * @param  array<int, array<string, mixed>>            $rows
     * @param  \Illuminate\Support\Collection<int, mixed>  $existingByIndex  logs keyed by index
     * @return array<int, array<string, mixed>>
     */
    private function applyBankingFeeCascade(array $rows, $existingByIndex): array
    {
        $prevNet = null;

        foreach ($rows as &$row) {
            $idx        = (int) ($row['index'] ?? 0);
            $bankingFee = (float) ($existingByIndex->get($idx)?->banking_fee ?? 0);

            if ($prevNet !== null) {
                $row['gross_amount'] = $prevNet;
            }

            $row['banking_fee'] = $bankingFee;
            $row['net_amount']  = round(
                (float) ($row['gross_amount'] ?? 0)
                - (float) ($row['discount'] ?? $row['commission_discount'] ?? 0)
                - $bankingFee,
                2
            );

            $prevNet = $row['net_amount'];
        }
        unset($row);

        return $rows;
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
            Action::make('userManual')
                ->label('User Manual')
                ->icon('heroicon-o-book-open')
                ->color('gray')
                ->modalHeading('Instalments — User Manual')
                ->modalContent(function () {
                    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                    $disk = Storage::disk('s3');
                    $url  = $disk->temporaryUrl(
                        'user_manual/Installments_Module_Reference_v1.pdf',
                        now()->addMinutes(30),
                        [
                            'ResponseContentType'        => 'application/pdf',
                            'ResponseContentDisposition' => 'inline; filename="Instalments_User_Manual.pdf"',
                        ]
                    );
                    return view('filament.components.pdf-viewer', compact('url'));
                })
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            Action::make('applyBordereaux')
                ->label('Apply Bordereaux')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->modalHeading('Apply Bordereaux Adjustment')
                ->modalSubmitActionLabel('Apply & Recalculate')
                ->modalWidth('2xl')
                ->visible(fn () =>
                    ! $this->record->trashed() &&
                    in_array(
                        $this->record->operativeDoc?->business?->premium_type,
                        ['Estimated', 'Declared'],
                    )
                )
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('bordereaux_reference')
                                ->label('Bordereaux Reference')
                                ->placeholder('e.g. Q1-2026-BDX-001')
                                ->required()
                                ->maxLength(100)
                                ->columnSpan(2),

                            TextInput::make('reported_premium')
                                ->label('Reported Premium')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    $set('new_amount', round(
                                        (float) $state - (float) ($get('reported_claims') ?? 0),
                                        2
                                    ));
                                }),

                            TextInput::make('reported_claims')
                                ->label('Reported Claims')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->minValue(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    $set('new_amount', round(
                                        (float) ($get('reported_premium') ?? 0) - (float) $state,
                                        2
                                    ));
                                }),

                            TextInput::make('new_amount')
                                ->label('Net Amount to Receive')
                                ->numeric()
                                ->required()
                                ->helperText('Reported Premium − Reported Claims. You can adjust it manually.')
                                ->columnSpan(2),

                            FileUpload::make('evidence_path')
                                ->label('Evidence Files (Bordereaux)')
                                ->disk('s3')
                                ->directory('reinsurers/transactions/bordereaux_evidence')
                                ->visibility('public')
                                ->multiple()
                                ->acceptedFileTypes(['application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                ->maxSize(10240)
                                ->columnSpan(2),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3)
                                ->columnSpan(2),
                        ]),
                ])
                ->action(function (array $data) {
                    $this->applyBordereaux($data);

                    $this->redirect(
                        static::getResource()::getUrl('edit', [
                            'record' => $this->record,
                        ])
                    );
                }),

            Action::make('recalculateLifecycle')
                ->label('Recalculate Lifecycle')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Recalculate Transaction Lifecycle')
                ->modalDescription(
                    'This will save the transaction and recalculate the transaction logs using the current transaction values. Operational fields such as sent date, received date, status, evidence and banking fee will be preserved.'
                )
                ->modalSubmitActionLabel('Save & Recalculate')
                ->visible(fn () => ! $this->record->trashed())
                ->action(function () {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);
                    $this->record = $this->record->fresh();
                    $this->recalculateLifecycle();

                    $this->redirect(
                        static::getResource()::getUrl('edit', [
                            'record' => $this->record,
                        ])
                    );
                }),

            RestoreAction::make()
                ->visible(fn () => $this->record->trashed()),

            ForceDeleteAction::make()
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

    #[On('transaction-status-updated')]
    public function refreshTransactionStatus(): void
    {
        $this->record = $this->record->fresh();

        $this->form->fill([
            ...$this->form->getRawState(),

            'transaction_status_display' =>
                $this->record->status?->transaction_status ?? 'Pending',

            'transaction_progress' =>
                $this->record->lifecycleProgressPercentage(),
        ]);
    }
}