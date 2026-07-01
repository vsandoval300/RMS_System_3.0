<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Models\User;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Transaction;
use App\Models\OperativeDoc;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Facades\Filament;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionLogsPreviewService;


class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    

    protected function authorizeAccess(): void
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        abort_unless(
            $user?->can('Business:AddTransaction') ?? false,
            403
        );
    }



    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transaction created')
            ->body('The new Transaction has been created successfully.');
    }


    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label('Save & Create Another')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Create Transaction')
            ->modalDescription('Create this Transaction and start a new one?')
            ->modalSubmitActionLabel('Create & New')
            ->action(function () {

                // 1) conservar el documento actual
                $opDocumentId = $this->data['op_document_id'] ?? request()->query('op_document_id');

                // 2) ✅ usar createAnother() (NO redirecciona a View)
                $this->createAnother();

                // 3) si no hay documento, listo
                if (blank($opDocumentId)) {
                    return;
                }

                // 4) recalcular defaults para la siguiente transacción
                $nextIndex = Transaction::where('op_document_id', $opDocumentId)->count() + 1;
                $newId     = (string) Str::uuid();

                $opDoc = OperativeDoc::query()
                    ->whereKey($opDocumentId)
                    ->with('business:business_code,currency_id')
                    ->first();

                $currencyId = $opDoc?->business?->currency_id;
                $exchRate = ((int) $currencyId === 157) ? 1 : ($opDoc?->roe_fs ?? null);

                // 5) rellenar form conservando Document y nuevos index/id
                $this->form->fill([
                    'op_document_id' => $opDocumentId,
                    'index'          => $nextIndex,
                    'id'             => $newId,
                    'exch_rate'      => $exchRate,
                    'transaction_status_id'  => 1, 
                    'preview_logs'   => [],
                ]);
            });
    }


    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
        ->requiresConfirmation()
        ->modalHeading('Create Transaction')
        ->modalDescription('Are you sure you want to create this Transaction?')
        ->modalSubmitActionLabel('Create')
        ->action(function () {
            $this->create();

            // ✅ SOLO aquí te vas al index
            $this->redirect(static::getResource()::getUrl('index'));
        })
        ->action(function () {
                try {
                    $this->create();
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->unmountAction();
                    throw $e;
                }
            })
            ->keyBindings(['mod+s']);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCreateAnotherFormAction(),

            Action::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray')
                ->outlined(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getFormDefaults(): array
    {
        return [
            ...parent::getFormDefaults(),
            'op_document_id' => request()->query('op_document_id'), // 👈 aquí llega el id del operative_doc
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['amount'] ??= 0; // ✅ evita null y cumple NOT NULL
        return $data;
    }

    protected function afterFill(): void
    {
        $opDocumentId = request()->query('op_document_id');

        if (blank($opDocumentId)) {
            return;
        }

        $nextIndex = Transaction::where('op_document_id', $opDocumentId)->count() + 1;

        $opDoc = OperativeDoc::query()
            ->with('business')
            ->whereKey($opDocumentId)
            ->first();

        $currencyId = $opDoc?->business?->currency_id;

        $this->form->fill([
            ...$this->form->getRawState(),

            'op_document_id' => $opDocumentId,
            'index' => $nextIndex,
            'id' => (string) Str::uuid(),
            'exch_rate' => ((int) $currencyId === 157) ? 1 : ($opDoc?->roe_fs ?? null),
            'exchange_rate_locked' => ((int) $currencyId === 157),
            'transaction_status_id' => 1,
            'preview_logs' => [],
        ]);
    }

    protected function handleRecordCreation(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {

            $createdTransaction = null;
            $previousAutoBuildLogs = Transaction::$autoBuildLogs;

            try {
                Transaction::$autoBuildLogs = false;

                foreach ($data['transactions_batch'] ?? [] as $row) {

                    $proportion = ((float) str_replace([',', '%'], '', (string) ($row['proportion'] ?? 0))) / 100;

                    $transaction = Transaction::create([
                        'id' => $row['id'],
                        'index' => $row['index'],
                        'op_document_id' => $data['op_document_id'],
                        'transaction_type_id' => 1,
                        'transaction_status_id' => 1,
                        'due_date' => $row['due_date'],
                        'proportion' => $proportion,
                        'exch_rate' => $row['exch_rate'],
                        'remmitance_code' => null,
                        'amount' => 0,
                    ]);

                    $createdTransaction ??= $transaction;

                    $logs = app(TransactionLogsPreviewService::class)->build(
                        opDocumentId: (string) $data['op_document_id'],
                        typeId: 1,
                        proportion: $proportion,
                        exchRate: (float) $row['exch_rate'],
                        remittanceCode: null,
                        dueDate: $row['due_date'],
                        costSchemeId: ($row['cost_scheme_option'] ?? 'all') === 'all'
                            ? null
                            : (string) $row['cost_scheme_option'],
                    );

                    $lastLog = null;
                    foreach ($logs as $logRow) {
                        $lastLog = TransactionLog::create([
                            'transaction_id' => $transaction->id,
                            'index' => $logRow['index'] ?? null,
                            'deduction_type' => $logRow['deduction_id'] ?? null,
                            'from_entity' => $logRow['from_entity'] ?? null,
                            'to_entity' => $logRow['to_entity'] ?? null,
                            'proportion' => $logRow['proportion'] ?? $transaction->proportion,
                            'exch_rate' => $logRow['exchange_rate']
                                ?? $logRow['exch_rate']
                                ?? $transaction->exch_rate,
                            'commission_percentage' => $logRow['commission_percentage'] ?? 0,
                            'gross_amount' => $logRow['gross_amount'] ?? 0,
                            'gross_amount_calc' => $logRow['gross_amount'] ?? 0,
                            'commission_discount' => $logRow['discount']
                                ?? $logRow['commission_discount']
                                ?? 0,
                            'net_amount' => $logRow['net_amount'] ?? 0,
                            'sent_date' => null,
                            'received_date' => null,
                            'status' => 'Pending',
                            'evidence_path' => null,
                            'banking_fee' => $logRow['banking_fee'] ?? 0,
                        ]);
                    }

                    if ($lastLog && filled($row['due_date'])) {
                        $lastLog->updateQuietly(['due_date' => $row['due_date']]);
                    }
                }
            } finally {
                Transaction::$autoBuildLogs = $previousAutoBuildLogs;
            }

            return $createdTransaction;
        });
    }




    
}
