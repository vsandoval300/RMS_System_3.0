<?php

namespace App\Filament\Resources\Industries\Pages;

use Filament\Support\Enums\Width;
use Filament\Schemas\Components\View;
use App\Filament\Resources\Industries\IndustryResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;

class ViewIndustries extends ViewRecord
{
    protected static string $resource = IndustryResource::class;

    protected Width|string|null $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Industry');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('auditInfo')
                ->label('Audit info')
                ->icon('heroicon-o-clipboard-document-list')
                ->modalHeading(' ')
                ->modalDescription('Review the full change history for this record, including who modified it and when.')
                ->modalWidth('4xl')
                ->modalSubmitAction(false)
                ->closeModalByClickingAway()
                ->schema(function () {

                    return [

                        // ── Change history (vista Blade que ya tienes) ──
                        View::make('filament.resources.audit.audit-logs')
                            ->viewData([
                                'logs' => $this->getRecord()
                                    ->auditLogs()
                                    ->with('user')
                                    ->latest()
                                    ->get(),
                            ])
                            ->columnSpanFull(),
                    ];
                }),

            Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->outlined()
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
