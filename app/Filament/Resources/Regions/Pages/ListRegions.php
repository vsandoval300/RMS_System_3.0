<?php

namespace App\Filament\Resources\Regions\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Regions\RegionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
