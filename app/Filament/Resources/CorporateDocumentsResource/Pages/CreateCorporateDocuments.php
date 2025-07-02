<?php

namespace App\Filament\Resources\CorporateDocumentsResource\Pages;

use App\Filament\Resources\CorporateDocumentsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCorporateDocuments extends CreateRecord
{
    protected static string $resource = CorporateDocumentsResource::class;
    /**
     * A dónde redirige el botón “Create”
     */
    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }




}
