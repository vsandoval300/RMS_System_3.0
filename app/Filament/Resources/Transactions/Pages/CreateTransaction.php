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


class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    /* protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    } */

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

                $currencyId = OperativeDoc::query()
                    ->whereKey($opDocumentId)
                    ->with('business:business_code,currency_id')
                    ->first()
                    ?->business
                    ?->currency_id;

                $exchRate = ((int) $currencyId === 157) ? 1 : null;

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
        return Action::make('create')
        ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
        ->requiresConfirmation()
        ->modalHeading('Create Transaction')
        ->modalDescription('Are you sure you want to create this Transaction?')
        ->modalSubmitActionLabel('Create')
        ->action(function () {
            $this->create();

            // ✅ SOLO aquí te vas al index
            $this->redirect(static::getResource()::getUrl('index'));
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
}
