<?php

namespace App\Filament\Resources\Directors\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Directors\DirectorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDirectors extends ListRecords
{
    protected static string $resource = DirectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Director')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Director')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
