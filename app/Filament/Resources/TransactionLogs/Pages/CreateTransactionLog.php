<?php

namespace App\Filament\Resources\TransactionLogs\Pages;

use App\Filament\Resources\TransactionLogs\TransactionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionLog extends CreateRecord
{
    protected static string $resource = TransactionLogResource::class;
}
