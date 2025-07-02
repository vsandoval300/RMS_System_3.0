<?php

namespace App\Filament\Resources\BanksResource\Pages;

use App\Filament\Resources\BanksResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBanks extends CreateRecord
{
    protected static string $resource = BanksResource::class;

     protected function getRedirectUrl(): string
        {
            // Vuelve al listado después de guardar
            return static::getResource()::getUrl('index');
        }
}
