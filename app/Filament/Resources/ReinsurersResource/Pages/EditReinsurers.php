<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use Filament\Actions;
use Filament\Actions\Action;
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
   /*  protected function getHeaderActions(): array
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
    } */

    /*--------------------------------------------------------------
     | 3. Quitar las acciones del pie del formulario
     --------------------------------------------------------------*/
    /* protected function getFormActions(): array
    {
        return [];              // así ya no se duplican abajo :contentReference[oaicite:1]{index=1}
    } */

    /*--------------------------------------------------------------
     | Gathered Relation Managers with Resource 
     --------------------------------------------------------------*/
    public function getContentTabLabel(): ?string
    {
        return 'Reinsurer Details';
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    /*--------------------------------------------------------------
     | Dinamic Name 
     --------------------------------------------------------------*/
    public function getTitle(): string
    {
        return 'Edit – ' . ($this->record?->name ?? 'Reinsurer');
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
            ->modalHeading('Save Reinsurer')
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