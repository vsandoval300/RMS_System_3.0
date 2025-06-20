<?php

namespace App\Filament\Resources\OperativeStatusesResource\Pages;

use App\Filament\Resources\OperativeStatusesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperativeStatuses extends ListRecords
{
    protected static string $resource = OperativeStatusesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
