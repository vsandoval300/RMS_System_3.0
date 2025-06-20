<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReinsurers extends EditRecord
{
    protected static string $resource = ReinsurersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
