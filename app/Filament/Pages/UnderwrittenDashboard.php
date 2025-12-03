<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;   // 👈 IMPORTANTE

class UnderwrittenDashboard extends Page
{

    use HasPageShield;   // 👈 ESTE TRAIT ACTIVA LOS PERMISOS PARA LA PAGE
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Underwritten';

    protected static string $view = 'filament.pages.underwritten-dashboard';

     /** Qué widgets mostrar en la página */
    /* protected function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\UnderwrittenBusiness::class, // ← nuestro line chart
        ];
    }
 */
    

}
