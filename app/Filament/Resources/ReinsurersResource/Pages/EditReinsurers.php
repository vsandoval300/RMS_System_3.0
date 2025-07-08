<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditReinsurers extends EditRecord
{
    protected static string $resource = ReinsurersResource::class;

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
    protected function getHeaderActions(): array
    {
        return [
            // Botón “Save changes”
            $this->getSaveFormAction()
                ->label('Save changes')
                ->formId('form')      // ¡clave! indica a qué <form> pertenece :contentReference[oaicite:0]{index=0}
                ->keyBindings(['mod+s']),

            // Botón “Cancel”
            $this->getCancelFormAction()
                ->label('Cancel'),
        ];
    }

    /*--------------------------------------------------------------
     | 3. Quitar las acciones del pie del formulario
     --------------------------------------------------------------*/
    protected function getFormActions(): array
    {
        return [];              // así ya no se duplican abajo :contentReference[oaicite:1]{index=1}
    }
}