<?php

namespace App\Filament\Resources\Currencies\Pages;

use App\Filament\Resources\Currencies\CurrenciesResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCurrencies extends CreateRecord
{
    protected static string $resource = CurrenciesResource::class;
    /**
     * A dónde redirige el botón “Create”
     */
    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Currency created')
            ->body('The new Currency has been created successfully.');
    }


    /**
     * 👉 Personalizamos SOLO el botón "Create"
     *     para que muestre un modal de confirmación.
     */
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->label(
                __('filament-panels::resources/pages/create-record.form.actions.create.label')
            )
            ->requiresConfirmation()
            ->modalHeading('Create Currency')
            ->modalDescription('Are you sure you want to create this Currency?')

            ->action(function () {

                // VALIDAR
                $this->validate();

                // CREAR
                $this->create();
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
            // ⬅️ aquí USAMOS el botón definido arriba
            $this->getCreateFormAction(),

            Action::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray')
                ->outlined(),
        ];
    }
}
