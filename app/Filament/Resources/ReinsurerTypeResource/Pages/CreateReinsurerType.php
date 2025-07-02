<?php

namespace App\Filament\Resources\ReinsurerTypeResource\Pages;

use App\Filament\Resources\ReinsurerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReinsurerType extends CreateRecord
{
    protected static string $resource = ReinsurerTypeResource::class;

     protected function getRedirectUrl(): string
        {
            // Vuelve al listado después de guardar
            return static::getResource()::getUrl('index');
        }
}
