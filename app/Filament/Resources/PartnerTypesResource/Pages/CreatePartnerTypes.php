<?php

namespace App\Filament\Resources\PartnerTypesResource\Pages;

use App\Filament\Resources\PartnerTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerTypes extends CreateRecord
{
    protected static string $resource = PartnerTypesResource::class;

     protected function getRedirectUrl(): string
        {
            // Vuelve al listado después de guardar
            return static::getResource()::getUrl('index');
        }
}
