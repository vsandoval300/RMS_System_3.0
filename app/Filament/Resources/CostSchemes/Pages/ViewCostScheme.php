<?php

namespace App\Filament\Resources\CostSchemes\Pages;

use Filament\Support\Enums\Width;
use Filament\Schemas\Components\View;
use App\Filament\Resources\CostSchemes\CostSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms;

class ViewCostScheme extends ViewRecord
{
     protected static string $resource = CostSchemeResource::class;
    
    protected Width|string|null $maxContentWidth = '7xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'CostScheme');
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

                ->modalWidth('4xl')
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
}