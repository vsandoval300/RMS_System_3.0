<?php

namespace App\Filament\Resources\CostSchemeResource\Pages;

use App\Filament\Resources\CostSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCostSchemes extends ListRecords
{
    protected static string $resource = CostSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
