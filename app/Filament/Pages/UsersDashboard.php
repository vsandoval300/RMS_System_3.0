<?php

namespace App\Filament\Pages;

use App\Filament\User\Widgets\LoginActivityChart;
use App\Filament\User\Widgets\TopActiveUsers;
use App\Filament\User\Widgets\UsersLoginsChart;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\User\Widgets\UserStatistics;
use Filament\Pages\Dashboard;

class UsersDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?int    $navigationSort  = 40;
    protected static ?string $navigationGroup = 'Security';

    protected static string $view = 'filament.pages.users-dashboard';

    // Widgets que se muestran arriba del contenido
    protected function getHeaderWidgets(): array
    {
        return [
            UserStatistics::class,
            LoginActivityChart::class,
            TopActiveUsers::class,
            UsersLoginsChart::class
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}