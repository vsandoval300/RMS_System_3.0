<?php

namespace App\Filament\Resources\ProducersResource\Pages;

use App\Filament\Resources\ProducersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducers extends ListRecords
{
    protected static string $resource = ProducersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
