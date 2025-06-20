<?php

namespace App\Filament\Resources\ReinsurerTypeResource\Pages;

use App\Filament\Resources\ReinsurerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReinsurerTypes extends ListRecords
{
    protected static string $resource = ReinsurerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
