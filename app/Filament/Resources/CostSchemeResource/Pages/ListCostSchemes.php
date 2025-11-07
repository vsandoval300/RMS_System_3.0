<?php

namespace App\Filament\Resources\CostSchemeResource\Pages;

use App\Filament\Resources\CostSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCostSchemes extends ListRecords
{
    protected static string $resource = CostSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Placement Scheme')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Placement Scheme')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
