<?php

namespace App\Filament\Resources\BankAccountsResource\Pages;

use App\Filament\Resources\BankAccountsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Closure;                                  // ← 1) importa Closure

class ListBankAccounts extends ListRecords
{
    protected static string $resource = BankAccountsResource::class;

    // 2) desactiva el enlace de la fila
    protected function getTableRecordUrlUsing(): ?Closure
    {
        return null;                          // ← con null no genera URL
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Bank Account')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Bank Account')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}

