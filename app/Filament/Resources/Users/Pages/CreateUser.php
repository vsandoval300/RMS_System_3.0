<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * A dónde redirige el botón “Create”
     */
    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }

    /* protected function afterCreate(): void
    {
        $this->record->syncRoles($this->form->getState()['roles'] ?? []);
    } */
   protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User created')
            ->body('The new User has been created successfully.');
    }


    /**
     * 👉 Personalizamos SOLO el botón "Create"
     *     para que muestre un modal de confirmación.
     */
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            // label por defecto de Filament
            ->requiresConfirmation()
            ->modalHeading('Create User')
            ->modalDescription('Are you sure you want to create this User?')
            ->modalSubmitActionLabel('Create')
            // qué hacer cuando el usuario confirma en el modal
            ->action(function () {
                try {
                    $this->create();
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->unmountAction();
                    throw $e;
                }
            })
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
}
