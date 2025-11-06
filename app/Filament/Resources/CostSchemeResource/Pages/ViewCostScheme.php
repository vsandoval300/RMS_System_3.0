<?php

namespace App\Filament\Resources\CostSchemeResource\Pages;

use App\Filament\Resources\CostSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCostScheme extends ViewRecord
{
     protected static string $resource = CostSchemeResource::class;
    
    protected ?string $maxContentWidth = '7xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'CostScheme');
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