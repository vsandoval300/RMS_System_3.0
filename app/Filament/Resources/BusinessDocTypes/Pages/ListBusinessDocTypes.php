<?php

namespace App\Filament\Resources\BusinessDocTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\BusinessDocTypes\BusinessDocTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessDocTypes extends ListRecords
{
    protected static string $resource = BusinessDocTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Document Type')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Document Type')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
