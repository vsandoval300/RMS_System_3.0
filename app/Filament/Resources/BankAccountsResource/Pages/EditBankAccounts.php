<?php

namespace App\Filament\Resources\BankAccountsResource\Pages;

use App\Filament\Resources\BankAccountsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankAccounts extends EditRecord
{
    protected static string $resource = BankAccountsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
