<?php

namespace App\Filament\Resources\PaymentFlow\Pages;

use App\Filament\Resources\PaymentFlow\PaymentFlowResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentFlow extends ListRecords
{
    protected static string $resource = PaymentFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
