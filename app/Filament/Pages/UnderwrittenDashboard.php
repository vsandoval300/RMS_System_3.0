<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UnderwrittenDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Underwritten';

    protected static string $view = 'filament.pages.underwritten-dashboard';

}
