<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBusiness extends CreateRecord
{
    protected static string $resource = BusinessResource::class;

    /**
     * A dónde redirige el botón “Create”
     */
    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }
}
