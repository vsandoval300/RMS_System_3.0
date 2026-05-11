<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompaniesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class CreateCompanies extends CreateRecord
{
    protected static string $resource = CompaniesResource::class;

    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Company created')
            ->body('The new Company has been created successfully.');
    }


    /**
     * 👉 Personalizamos SOLO el botón "Create"
     *     para que muestre un modal de confirmación.
     */
    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            // label por defecto de Filament
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->requiresConfirmation()
            ->modalHeading('Create Company')
            ->modalDescription('Are you sure you want to create this Company?')
            ->modalSubmitActionLabel('Create')
            // qué hacer cuando el usuario confirma en el modal
            ->action(fn () => $this->create())
            ->keyBindings(['mod+s']); // ⌘+S / Ctrl+S
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

    public function getMaxContentWidth(): ?string
    {
        return '7xl';
    }
}
