<?php

namespace App\Filament\Resources\CostSchemeResource\Pages;

use App\Filament\Resources\CostSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCostScheme extends EditRecord
{
    protected static string $resource = CostSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
