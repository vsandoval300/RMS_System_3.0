<?php

namespace App\Filament\Resources\CoveragesResource\Pages;

use App\Filament\Resources\CoveragesResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewCoverages extends ViewRecord
{
    protected static string $resource = CoveragesResource::class;
    
    protected ?string $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Coverage');
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
