<?php

namespace App\Filament\Resources\Companies\Pages;

use Filament\Support\Enums\Width;
use Filament\Schemas\Components\View;
use App\Filament\Resources\Companies\CompaniesResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Actions\Action;
use Filament\Forms;

class ViewCompanies extends ViewRecord
{
    protected static string $resource = CompaniesResource::class;
    
    protected Width|string|null $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Company');
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
