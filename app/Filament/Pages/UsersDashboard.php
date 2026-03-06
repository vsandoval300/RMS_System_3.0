<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Resources\StaticsDashboardResource\Widgets\UserStatistics;
use App\Filament\Resources\StatsOverviewResource\Widgets\UserStatistics as WidgetsUserStatistics;

class UserDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?int    $navigationSort  = -122;
    protected static ?string $navigationGroup = 'Security';

    protected static string $view = 'filament.pages.users-dashboard';

    // Widgets que se muestran arriba del contenido
    protected function getHeaderWidgets(): array
    {
        return [
            UserStatistics::class,
        ];
    }
}