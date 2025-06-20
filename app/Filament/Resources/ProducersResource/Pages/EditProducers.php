<?php

namespace App\Filament\Resources\ProducersResource\Pages;

use App\Filament\Resources\ProducersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProducers extends EditRecord
{
    protected static string $resource = ProducersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
