<?php

namespace App\Filament\Resources\ClientsResource\Pages;

use App\Filament\Resources\ClientsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClients extends CreateRecord
{
    protected static string $resource = ClientsResource::class;

    protected function getRedirectUrl(): string
        {
            // Vuelve al listado después de guardar
            return static::getResource()::getUrl('index');
        }
}
