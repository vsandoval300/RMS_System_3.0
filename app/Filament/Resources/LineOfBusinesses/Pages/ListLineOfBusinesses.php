<?php

namespace App\Filament\Resources\LineOfBusinesses\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\LineOfBusinesses\LineOfBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLineOfBusinesses extends ListRecords
{
    protected static string $resource = LineOfBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Line of Business')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Line of Business')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
