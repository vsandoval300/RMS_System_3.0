<?php

namespace App\Filament\Resources\ProducersResource\Pages;

use App\Filament\Resources\ProducersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducers extends ListRecords
{
    protected static string $resource = ProducersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Producer')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Producer')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
