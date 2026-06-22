<?php

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Resources\Countries\CountriesResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCountries extends EditRecord
{
    protected static string $resource = CountriesResource::class;

    /* protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    } */


    // 👇 Agrega este método
    // 👇 Ajustar la firma a ?string
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
            ->modalHeading('Save Country')
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
