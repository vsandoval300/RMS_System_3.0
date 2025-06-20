<?php

namespace App\Filament\Resources\CorporateDocumentsResource\Pages;

use App\Filament\Resources\CorporateDocumentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCorporateDocuments extends ListRecords
{
    protected static string $resource = CorporateDocumentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
