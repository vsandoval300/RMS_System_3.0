<?php

namespace App\Filament\Resources\StaticsDashboardResource\Pages;

use App\Filament\Resources\StaticsDashboardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaticsDashboard extends EditRecord
{
    protected static string $resource = StaticsDashboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
