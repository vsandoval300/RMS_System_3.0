<?php

namespace App\Filament\Resources\ManagerResource\Pages;

use App\Filament\Resources\ManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateManager extends CreateRecord
{
    protected static string $resource = ManagerResource::class;

     protected function getRedirectUrl(): string
        {
            // Vuelve al listado después de guardar
            return static::getResource()::getUrl('index');
        }
}
