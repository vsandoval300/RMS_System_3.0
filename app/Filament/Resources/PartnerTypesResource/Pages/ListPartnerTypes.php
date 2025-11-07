<?php

namespace App\Filament\Resources\PartnerTypesResource\Pages;

use App\Filament\Resources\PartnerTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerTypes extends ListRecords
{
    protected static string $resource = PartnerTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Partner Type')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Partner Type')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
