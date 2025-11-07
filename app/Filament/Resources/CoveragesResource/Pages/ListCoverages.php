<?php

namespace App\Filament\Resources\CoveragesResource\Pages;

use App\Filament\Resources\CoveragesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCoverages extends ListRecords
{
    protected static string $resource = CoveragesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Coverage')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Coverage')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
