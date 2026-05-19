<?php

namespace App\Filament\Resources\Transactions\Pages;

use Filament\Support\Enums\Width;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\view;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;
    
    protected Width|string|null $maxContentWidth = '8xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Transaction');
    }

    protected function getRecordQuery(): Builder
    {
        return parent::getRecordQuery()->with([
            'type',
            'status',
            'operativeDoc.business.reinsurer',
            'remmitanceCode',
        ]);
    }
    /*  protected function getHeaderActions(): array
    {
        return [
            /* Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index')), 

            Actions\Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->outlined()
                ->url(static::getResource()::getUrl('index')),    
        ];
    } */

    protected function getHeaderActions(): array
    {
        return [
            
            Action::make('auditInfo')
                ->label('Audit info')
                ->icon('heroicon-o-clipboard-document-list')
                ->modalHeading('Audit info')
                ->modalWidth('4xl')        // 👈 aquí controlas el ancho del modal
                ->modalSubmitAction(false) // no necesitamos botón de "Save"
                ->closeModalByClickingAway() // opcional
                ->schema([
                    \Filament\Schemas\Components\View::make('filament.resources.audit.audit-logs')
                        ->viewData([
                            'logs' => $this->getRecord()
                                ->auditLogs()
                                ->with('user')
                                ->latest()
                                ->get(),
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