<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReinsurers extends CreateRecord
{
    protected static string $resource = ReinsurersResource::class;

    /**
     * A dónde redirige el botón “Create”
     */
    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }
}