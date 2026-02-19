<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Widgets\UnderwrittenBusiness;   // 👈 ESTE namespace
use App\Filament\Underwritten\Resources\NoResource\Widgets\PremiumForPeriod;

class UnderwrittenDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $navigationGroup = 'Underwritten';

    protected static string $view = 'filament.pages.underwritten-dashboard';

    // Widgets que se muestran arriba del contenido
    protected function getHeaderWidgets(): array
    {
        return [
            UnderwrittenBusiness::class,
            PremiumForPeriod::class
        ];
    }
}
