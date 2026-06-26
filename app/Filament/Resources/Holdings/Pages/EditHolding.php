<?php

namespace App\Filament\Resources\Holdings\Pages;

use App\Filament\Resources\Holdings\HoldingResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditHolding extends EditRecord
{
    protected static string $resource = HoldingResource::class;


    /*--------------------------------------------------------------
     | 1. Ocultar el botón Delete
     --------------------------------------------------------------*/
    /* public static function canDelete(Model $record): bool
    {
        return false;           // ningún usuario verá “Delete”
    } */

    /*--------------------------------------------------------------
     | 2. Mostrar Save & Cancel en la cabecera
     --------------------------------------------------------------*/
    /* protected function getHeaderActions(): array
    {
        return [
            // Botón “Save changes”
            $this->getSaveFormAction()
                ->label('Save changes')
                ->formId('form')      // ¡clave! indica a qué <form> pertenece :contentReference[oaicite:0]{index=0}
                ->action(function () {
                try {
                    $this->save();
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->unmountAction();
                    throw $e;
                }
            })
            ->keyBindings(['mod+s']),

            // Botón “Cancel”
            $this->getCancelFormAction()
                ->label('Cancel'),
        ];
    } */

    /*--------------------------------------------------------------
     | 3. Quitar las acciones del pie del formulario
     --------------------------------------------------------------*/
    /* protected function getFormActions(): array
    {
        return [];              // así ya no se duplican abajo :contentReference[oaicite:1]{index=1}
    } */


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
        return parent::getSaveFormAction()
            ->submit(null)
            // mismo label que Filament usa por defecto
            ->requiresConfirmation()
            ->modalHeading('Save Holding')
            ->modalDescription('Are you sure you want to save these changes?')  
            ->modalSubmitActionLabel('Save') 
            // qué hacer al confirmar en el modal
            ->action(function () {
                try {
                    $this->save();
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->unmountAction();
                    throw $e;
                }
            })
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
