<?php

namespace App\Filament\Resources\HoldingResource\Pages;

use App\Filament\Resources\HoldingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHoldings extends ListRecords
{
    protected static string $resource = HoldingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
