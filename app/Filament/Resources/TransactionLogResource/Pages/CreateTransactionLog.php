<?php

namespace App\Filament\Resources\TransactionLogResource\Pages;

use App\Filament\Resources\TransactionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionLog extends CreateRecord
{
    protected static string $resource = TransactionLogResource::class;
}
