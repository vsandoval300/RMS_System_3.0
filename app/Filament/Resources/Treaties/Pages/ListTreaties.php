<?php

namespace App\Filament\Resources\Treaties\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Treaties\TreatyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTreaties extends ListRecords
{
    protected static string $resource = TreatyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Treaty')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Treaty')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
