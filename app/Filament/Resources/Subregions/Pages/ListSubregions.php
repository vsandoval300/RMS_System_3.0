<?php

namespace App\Filament\Resources\Subregions\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Subregions\SubregionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubregions extends ListRecords
{
    protected static string $resource = SubregionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
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
