<?php

namespace App\Filament\Resources\TransactionLogResource\Pages;

use App\Filament\Resources\TransactionLogResource;
use Filament\Resources\Pages\EditRecord;

class EditTransactionLog extends EditRecord
{
    protected static string $resource = TransactionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // sin delete
        ];
    }
}




