<?php

namespace App\Filament\Resources\TransactionLogs\Pages;

use App\Filament\Resources\TransactionLogs\TransactionLogResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactionLogs extends ListRecords
{
    protected static string $resource = TransactionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 🚫 sin CreateAction
        ];
    }
}

