<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Resources extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Resources'; // 👈 nombre en menú lateral
    protected static bool $hasSidebarNavigation = true;      // 👈 activa el menú desplegable
}