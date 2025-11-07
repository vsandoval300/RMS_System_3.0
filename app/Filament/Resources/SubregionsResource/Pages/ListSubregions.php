<?php

namespace App\Filament\Resources\SubregionsResource\Pages;

use App\Filament\Resources\SubregionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubregions extends ListRecords
{
    protected static string $resource = SubregionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Subregion')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Subregion')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
