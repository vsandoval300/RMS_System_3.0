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

            Action::make('auditInfo')
                ->label('Audit info')
                ->icon('heroicon-o-clipboard-document-list')
                ->stickyModalHeader()

                ->extraModalWindowAttributes([
                    'class' => 'audit-modal',
                ])

                ->modalWidth('7xl')
                ->modalContent(fn () => view(
                    'filament.resources.audit.audit-logs',
                    [
                        'logs' => $this->getRecord()
                            ->auditLogs()
                            ->with('user')
                            ->latest()
                            ->get(),
                    ],
                ))
                ->modalSubmitAction(false)
                ->modalCancelAction(false),
                
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


