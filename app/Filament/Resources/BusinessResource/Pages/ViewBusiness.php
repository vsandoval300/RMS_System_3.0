<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Business;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;

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

    /* public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    } */

    public function getTitle(): string
    {
        return 'Business – [ ' . ($this->record?->business_code ?? 'Business') . ' ]';
    }

    protected function getHeaderActions(): array
    {
        return [
            /* Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index')), */

            Action::make('auditInfo')
                ->label('Audit info')
                ->icon('heroicon-o-clipboard-document-list')
                ->modalHeading('Audit info')
                ->modalWidth('7xl')        // 👈 aquí controlas el ancho del modal
                ->modalSubmitAction(false) // no necesitamos botón de "Save"
                ->closeModalByClickingAway() // opcional
                ->form([
                    Forms\Components\View::make('filament.resources.audit.audit-logs')
                        ->viewData([
                            // Pasamos el registro actual a la vista Blade
                            'record' => $this->getRecord(),
                        ])
                        ->columnSpanFull(),
                ]),   
                
            Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->outlined()
                ->url(static::getResource()::getUrl('index')),      
        ];
    }

}


