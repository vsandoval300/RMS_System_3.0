<?php

/* namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} */
// app/Filament/Resources/TransactionResource/Pages/EditTransaction.php
namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    // 👇 Permite cargar registros soft-deleted en /edit
    protected function getRecordQuery(): Builder
    {
        return parent::getRecordQuery()->withTrashed();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\RestoreAction::make()->visible(fn () => $this->record->trashed()),
            Actions\ForceDeleteAction::make()->visible(fn () => $this->record->trashed()),
            Actions\DeleteAction::make()->visible(fn () => ! $this->record->trashed()),
        ];
    }

    // (opcional) esconder el botón Save si está “trashed”
    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()->visible(fn () => ! $this->record->trashed());
    }
}
