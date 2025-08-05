<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReinsurer extends ViewRecord
{
    protected static string $resource = ReinsurersResource::class;

    public function getContentTabLabel(): ?string
    {
        return 'Reinsurer Details';
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }


}
