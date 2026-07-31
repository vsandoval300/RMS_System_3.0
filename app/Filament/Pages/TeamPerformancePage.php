<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class TeamPerformancePage extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel               = 'Team Performance';
    protected static ?string $title                         = 'Team Performance';
    protected static ?int    $navigationSort                = 2;
    protected static string|\UnitEnum|null $navigationGroup = 'Underwritten';

    protected string $view = 'filament.pages.team-performance-page';
}
