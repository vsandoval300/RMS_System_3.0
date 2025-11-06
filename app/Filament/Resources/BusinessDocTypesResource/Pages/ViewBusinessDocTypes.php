<?php

namespace App\Filament\Resources\BusinessDocTypesResource\Pages;

use App\Filament\Resources\BusinessDocTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBusinessDocTypes extends ViewRecord
{
    protected static string $resource = BusinessDocTypesResource::class;
    
    protected ?string $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'BusinessDocType');
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