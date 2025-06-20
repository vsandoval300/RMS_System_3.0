<?php

namespace App\Filament\Resources\LineOfBusinessResource\Pages;

use App\Filament\Resources\LineOfBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLineOfBusiness extends EditRecord
{
    protected static string $resource = LineOfBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
