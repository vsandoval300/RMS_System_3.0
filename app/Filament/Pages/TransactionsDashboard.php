<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Transactions\Widgets\TransactionsDashboardOverview;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class TransactionsDashboard extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?int    $navigationSort  = 0;
    protected static string|\UnitEnum|null $navigationGroup = 'Transactions';
    protected static ?string $title = 'Transactions Dashboard';

    protected string $view = 'filament.pages.transactions-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionsDashboardOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
