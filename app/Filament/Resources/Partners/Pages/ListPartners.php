<?php

namespace App\Filament\Resources\Partners\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Partners\PartnersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Partner')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Partner')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
