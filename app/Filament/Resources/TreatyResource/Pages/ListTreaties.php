<?php

namespace App\Filament\Resources\TreatyResource\Pages;

use App\Filament\Resources\TreatyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTreaties extends ListRecords
{
    protected static string $resource = TreatyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
