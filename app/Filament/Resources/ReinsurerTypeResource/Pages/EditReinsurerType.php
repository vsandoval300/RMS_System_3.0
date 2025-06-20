<?php

namespace App\Filament\Resources\ReinsurerTypeResource\Pages;

use App\Filament\Resources\ReinsurerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReinsurerType extends EditRecord
{
    protected static string $resource = ReinsurerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
