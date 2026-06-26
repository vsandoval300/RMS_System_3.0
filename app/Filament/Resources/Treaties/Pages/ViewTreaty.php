<?php

namespace App\Filament\Resources\Treaties\Pages;

use Filament\Support\Enums\Width;
use App\Filament\Resources\Treaties\TreatyResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Resources\Pages\view;
use Filament\Actions\Action;
use Filament\Forms;
use App\Models\AuditLog;
use App\Models\TreatyDoc;


class ViewTreaty extends viewRecord
{
    protected static string $resource = TreatyResource::class;

    protected Width|string|null $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->treaty_code ?? 'Treaty');
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
