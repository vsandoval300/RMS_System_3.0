<?php

namespace App\Filament\Resources\BusinessDocTypesResource\Pages;

use App\Filament\Resources\BusinessDocTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessDocTypes extends EditRecord
{
    protected static string $resource = BusinessDocTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
