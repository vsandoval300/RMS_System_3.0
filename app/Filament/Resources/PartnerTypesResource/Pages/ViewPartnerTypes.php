<?php

namespace App\Filament\Resources\PartnerTypesResource\Pages;

use App\Filament\Resources\PartnerTypesResource;
use Filament\Resources\Pages\view;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms;

class ViewPartnerTypes extends ViewRecord
{
    protected static string $resource = PartnerTypesResource::class;
    
    protected ?string $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'PartnerType');
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
