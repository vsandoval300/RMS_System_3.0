<?php

namespace App\Filament\Resources\Subregions\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Subregions\SubregionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubregions extends ListRecords
{
    protected static string $resource = SubregionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
