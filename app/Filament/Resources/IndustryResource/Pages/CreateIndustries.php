<?php

namespace App\Filament\Resources\IndustryResource\Pages;

use App\Filament\Resources\IndustryResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateIndustries extends CreateRecord
{
    protected static string $resource = IndustryResource::class;

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
            ->title('Industry created')
            ->body('The new Industry has been created successfully.');
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
            ->modalHeading('Create Industry')
            ->modalDescription('Are you sure you want to create this Industry?')
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

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray')
                ->outlined(),
        ];
    }
}
