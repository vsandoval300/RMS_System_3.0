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

    /* public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    } */

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Reinsurer');
    }

    protected function getHeaderActions(): array
    {
        return [
            /* Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index')), */

            Actions\Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->outlined()
                ->url(static::getResource()::getUrl('index')),    
        ];
    }


}
