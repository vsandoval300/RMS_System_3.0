<?php

namespace App\Filament\Resources\ClientsResource\Pages;

use App\Filament\Resources\ClientsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClients extends ViewRecord
{
    protected static string $resource = ClientsResource::class;


    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Client');
    }
}


