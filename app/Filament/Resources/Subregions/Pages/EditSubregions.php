<?php

namespace App\Filament\Resources\Subregions\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Subregions\SubregionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubregions extends EditRecord
{
    protected static string $resource = SubregionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
