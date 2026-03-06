<?php

namespace App\Filament\Resources\StaticsDashboardResource\Pages;

use App\Filament\Resources\StaticsDashboardResource;
use App\Filament\Resources\StaticsDashboardResource\Widgets\UserStatistics;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaticsDashboards extends ListRecords
{
    protected static string $resource = StaticsDashboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatistics::class,
        ];
    }
}
