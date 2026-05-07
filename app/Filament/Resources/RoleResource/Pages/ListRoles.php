<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    /* protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    } */

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Role')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Role')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
