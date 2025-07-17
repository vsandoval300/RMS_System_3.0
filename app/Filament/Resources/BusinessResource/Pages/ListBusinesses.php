<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BusinessResource\Widgets\BusinessStatsOverview;

class ListBusinesses extends ListRecords
{
    protected static string $resource = BusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // 🔥 Esto es lo que faltaba
    protected function getHeaderWidgets(): array
    {
        return [
            BusinessStatsOverview::class,
        ];
    }
}
