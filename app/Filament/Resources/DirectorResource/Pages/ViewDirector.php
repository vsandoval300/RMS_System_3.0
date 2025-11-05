<?php

namespace App\Filament\Resources\DirectorResource\Pages;

use App\Filament\Resources\DirectorResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewDirector extends ViewRecord
{
    protected static string $resource = DirectorResource::class;

    protected ?string $maxContentWidth = '4xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Director');
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
