<?php

namespace App\Filament\Resources\Holdings\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Holdings\HoldingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHoldings extends ListRecords
{
    protected static string $resource = HoldingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Holding')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Holding')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
