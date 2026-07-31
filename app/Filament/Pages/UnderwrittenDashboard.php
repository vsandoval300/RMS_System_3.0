<?php

namespace App\Filament\Pages;

use App\Filament\Underwritten\Widgets\UnderwrittenOverview;
use App\Filament\Underwritten\Widgets\UnderwrittenProfile;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class UnderwrittenDashboard extends Page
{
    use HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?int    $navigationSort  = 1;
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';

    protected string $view = 'filament.pages.underwritten-dashboard';

    public string $activeTab = 'portfolio';
}
