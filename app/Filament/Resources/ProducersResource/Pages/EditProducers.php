<?php

namespace App\Filament\Resources\ProducersResource\Pages;

use App\Filament\Resources\ProducersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;

class EditProducers extends EditRecord
{
    protected static string $resource = ProducersResource::class;

    /* protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    } */


    /*--------------------------------------------------------------
     | 1. Ocultar el botón Delete
     --------------------------------------------------------------*/
    public static function canDelete(Model $record): bool
    {
        return false;           // ningún usuario verá “Delete”
    }

    /* protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    } */

    /*--------------------------------------------------------------
     | Dinamic Name 
     --------------------------------------------------------------*/
    public function getTitle(): string
    {
        return 'Edit – ' . ($this->record?->name ?? 'Producer');
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
            ->modalHeading('Save Producer')
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
