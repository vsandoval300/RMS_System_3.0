<?php

namespace App\Filament\Resources\LineOfBusinessResource\Pages;

use App\Filament\Resources\LineOfBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLineOfBusinesses extends ListRecords
{
    protected static string $resource = LineOfBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
