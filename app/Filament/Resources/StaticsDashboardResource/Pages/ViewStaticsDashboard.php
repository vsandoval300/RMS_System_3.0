<?php

namespace App\Filament\Resources\StaticsDashboardResource\Pages;

use App\Filament\Resources\StaticsDashboardResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStaticsDashboard extends ViewRecord
{
    protected static string $resource = StaticsDashboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
