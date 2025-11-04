<?php

namespace App\Filament\Resources\ClientsResource\Pages;

use App\Filament\Resources\ClientsResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;                    // ← ya incluye Action
use Filament\Actions\Action;         // ✔  la clase Action
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Database\Eloquent\Model;

class EditClients extends EditRecord
{
    protected static string $resource = ClientsResource::class;

    /*--------------------------------------------------------------
     | 1. Eliminar por completo el botón Delete
     --------------------------------------------------------------*/
    public static function canDelete(Model $record): bool
    {
        return false;                   // no se mostrará “Delete”
    }

    /*--------------------------------------------------------------
     | 2. Botones visibles en la BARRA de encabezado
     --------------------------------------------------------------*/
    /* protected function getHeaderActions(): array
    {
        return [
        Actions\Action::make('saveAndClose')
            ->label('Save & Close')
            ->color('primary')
            ->keyBindings(['mod+s'])      // ⌘S / Ctrl-S
            ->action('saveAndClose'),     // 👈 Llama al método de la página

        Actions\Action::make('cancel')
            ->label('Cancel')
            ->color('gray')
            ->url(ClientsResource::getUrl()),
        ];
    } */

    /*--------------------------------------------------------------
     | 3. Ocultar los botones que Filament coloca en el pie
     --------------------------------------------------------------*/
    /* protected function getSaveFormAction(): Action
    {
        // devuelve la acción creada por Filament, pero oculta
        return parent::getSaveFormAction()->hidden();
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->hidden();
    } */

    /*--------------------------------------------------------------
     | 4. Acción que ejecuta nuestro botón “saveAndClose”
     --------------------------------------------------------------*/
    /* public function saveAndClose(): void
    {
        // Guarda SIN redirigir (el segundo argumento = false)
        $this->save(false);

        // Notificación con único botón
        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->persistent()
            ->actions([
                NotificationAction::make('close')
                    ->label('Close')
                    ->button()
                    ->url(ClientsResource::getUrl()),
            ])
            ->send();

        // Por si el usuario cierra la notificación con la ✕
        $this->redirect(ClientsResource::getUrl());
    } */

    /* -----------------------------------------------------------------
     | 5. Desactiva la notificación automática de Filament
     |-----------------------------------------------------------------*/
    /* protected function getSavedNotification(): ?Notification
    {
        return null; // usamos la nuestra en saveAndClose()
    } */


    public function getTitle(): string
    {
        return 'Edit – ' . ($this->record?->name ?? 'Client');
    }









    protected function getRedirectUrl(): ?string
    {
        // Después de guardar cambios → vuelve al listado
        return static::getResource()::getUrl('index');
        // o: return CostSchemeResource::getUrl('index');
    }

    /**
     * 👉 Personalizamos SOLO el botón "Save"
     *     para que muestre un modal de confirmación.
     */
    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            // mismo label que Filament usa por defecto
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->requiresConfirmation()
            ->modalHeading('Save Client')
            ->modalDescription('Are you sure you want to save these changes?')  
            ->modalSubmitActionLabel('Save') 
            // qué hacer al confirmar en el modal
            ->action(fn () => $this->save())
            ->keyBindings(['mod+s']); // ⌘+S / Ctrl+S
    }

    /**
     * 👉 Opcional: personalizar las acciones debajo del formulario
     *     (Save + Cancel).
     */
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
