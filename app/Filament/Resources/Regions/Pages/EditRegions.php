<?php

namespace App\Filament\Resources\Regions\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Regions\RegionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegions extends EditRecord
{
    protected static string $resource = RegionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
