<?php

namespace App\Filament\Resources\OperativeStatuses\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\OperativeStatuses\OperativeStatusesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperativeStatuses extends ListRecords
{
    protected static string $resource = OperativeStatusesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Operative Status')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Operative Status')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
