<?php

namespace App\Filament\Resources\Businesses\Pages;

use Filament\Schemas\Components\View;
use App\Filament\Resources\Businesses\BusinessResource;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Business;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
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
                ->modalContent(fn () => view(
                'filament.resources.audit.audit-logs',
                [
                    'record' => $this->getRecord(),

                ],
            ))

            ->modalSubmitAction(false)
            ->modalCancelAction(false)

            // 👇 MUY IMPORTANTE
            ->modalWidth('4xl')
            ->stickyModalHeader(),
            // 👇 controlamos altura REAL del modal
            // ->extraModalWindowAttributes([
            //     'class' => '!p-0 !overflow-hidden !max-h-[80vh]',
            // ]),   
                
            Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->outlined()
                ->url(static::getResource()::getUrl('index')),      
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return '7xl';
    }

}


