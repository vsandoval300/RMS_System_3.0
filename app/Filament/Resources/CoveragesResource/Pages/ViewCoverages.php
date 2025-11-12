<?php

namespace App\Filament\Resources\CoveragesResource\Pages;

use App\Filament\Resources\CoveragesResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;

class ViewCoverages extends ViewRecord
{
    protected static string $resource = CoveragesResource::class;
    
    protected ?string $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Coverage');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('auditInfo')
                ->label('Audit info')
                ->icon('heroicon-o-clipboard-document-list')
                ->modalHeading(' ')
                ->modalWidth('4xl')
                ->modalSubmitAction(false)
                ->closeModalByClickingAway()
                ->form(function () {
                    $record = $this->getRecord();

                    return [

                        // ── Change history (vista Blade que ya tienes) ──
                        Forms\Components\View::make('filament.resources.audit.audit-logs')
                            ->viewData([
                                'record' => $record,
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
