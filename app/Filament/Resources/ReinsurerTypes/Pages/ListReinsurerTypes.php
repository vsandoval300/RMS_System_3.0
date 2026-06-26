<?php

namespace App\Filament\Resources\ReinsurerTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ReinsurerTypes\ReinsurerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReinsurerTypes extends ListRecords
{
    protected static string $resource = ReinsurerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Reinsurer Type')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Reinsurer Type')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
