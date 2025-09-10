<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Business;

class ViewBusiness extends ViewRecord
{
    protected static string $resource = BusinessResource::class;

    protected function resolveRecord(int|string $key): Business
    {
        return Business::with([
            'reinsurer',
            'producer',
            'currency',
            'region',
            'operativeDocs',
            'parent',
            'renewedFrom',
        ])->findOrFail($key);
    }

    public function getContentTabLabel(): ?string
    {
        return 'Business Details';
    }

  /*   public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    } */

    public function getTitle(): string
    {
        return 'Business – [ ' . ($this->record?->business_code ?? 'Business') . ' ]';
    }

}
