<?php

namespace App\Filament\Resources\CoveragesResource\Pages;

use App\Filament\Resources\CoveragesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCoverages extends ListRecords
{
    protected static string $resource = CoveragesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
