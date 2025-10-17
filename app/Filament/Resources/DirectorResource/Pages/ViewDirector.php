<?php

namespace App\Filament\Resources\DirectorResource\Pages;

use App\Filament\Resources\DirectorResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewDirector extends ViewRecord
{
    protected static string $resource = DirectorResource::class;

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Director');
    }
    
}
