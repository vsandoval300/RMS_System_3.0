<?php

namespace App\Filament\Resources\Businesses\Pages;

use App\Filament\Resources\Businesses\BusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;



class EditBusiness extends EditRecord
{
    protected static string $resource = BusinessResource::class;


   /*  protected function getHeaderActions(): array
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

    public function getContentTabLabel(): ?string
    {
        return 'Business Details';
    }

    public function getContentTabIcon(): ?string
    {
        // icono del tab principal
        return 'heroicon-o-briefcase';
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
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
        return 'Edit – ' . ($this->record?->name ?? 'Business');
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('edit', [
            'record' => $this->record,
        ]);
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
            ->modalHeading('Save Business')
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


    /* protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index')), 


            Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->outlined()
                ->url(static::getResource()::getUrl('index')),      
        ];
    }*/
    

}
