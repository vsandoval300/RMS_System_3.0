<?php

namespace App\Filament\Resources\BankAccountsResource\Pages;

use App\Filament\Resources\BankAccountsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBankAccounts extends CreateRecord
{
    protected static string $resource = BankAccountsResource::class;

     protected function getRedirectUrl(): string
        {
            // Vuelve al listado después de guardar
            return static::getResource()::getUrl('index');
        }
}
