<?php

namespace App\Filament\Resources\BanksResource\Pages;

use App\Filament\Resources\BanksResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanks extends EditRecord
{
    protected static string $resource = BanksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
