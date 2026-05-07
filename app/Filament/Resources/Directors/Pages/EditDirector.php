<?php

namespace App\Filament\Resources\Directors\Pages;

use App\Filament\Resources\Directors\DirectorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;         // ✔  la clase Action
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;


class EditDirector extends EditRecord
{
    protected static string $resource = DirectorResource::class;

    /*--------------------------------------------------------------
     | 1. Ocultar el botón Delete
     --------------------------------------------------------------*/
    public static function canDelete(Model $record): bool
    {
        return false;           // ningún usuario verá “Delete”
    }

    /*--------------------------------------------------------------
     | 2. Mostrar Save & Cancel en la cabecera
     --------------------------------------------------------------*/
   /*  protected function getHeaderActions(): array
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
            ->url(DirectorResource::getUrl()),
        ];
    } */

    /*--------------------------------------------------------------
     | 3. Ocultar los botones que Filament coloca en el pie
     --------------------------------------------------------------*/
   /*  protected function getSaveFormAction(): Action
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
                    ->url(DirectorResource::getUrl()),
            ])
            ->send();

        // Por si el usuario cierra la notificación con la ✕
        $this->redirect(DirectorResource::getUrl());
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
        return 'Edit – ' . ($this->record?->name ?? 'Director') . ' ' .($this->record?->surname ?? 'Director');
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
            ->modalHeading('Save Director')
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
