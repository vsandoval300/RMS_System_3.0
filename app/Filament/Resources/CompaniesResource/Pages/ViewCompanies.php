<?php

namespace App\Filament\Resources\CompaniesResource\Pages;

use App\Filament\Resources\CompaniesResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewCompanies extends ViewRecord
{
    protected static string $resource = CompaniesResource::class;
    
    protected ?string $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Company');
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
