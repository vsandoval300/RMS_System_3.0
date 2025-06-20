<?php

namespace App\Filament\Resources\BusinessDocTypesResource\Pages;

use App\Filament\Resources\BusinessDocTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessDocTypes extends ListRecords
{
    protected static string $resource = BusinessDocTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
