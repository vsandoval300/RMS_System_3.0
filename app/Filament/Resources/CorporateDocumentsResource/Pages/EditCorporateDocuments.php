<?php

namespace App\Filament\Resources\CorporateDocumentsResource\Pages;

use App\Filament\Resources\CorporateDocumentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCorporateDocuments extends EditRecord
{
    protected static string $resource = CorporateDocumentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
