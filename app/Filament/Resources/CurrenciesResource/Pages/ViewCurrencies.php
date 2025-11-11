<?php

namespace App\Filament\Resources\CurrenciesResource\Pages;

use App\Filament\Resources\CurrenciesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms;

class ViewCurrencies extends ViewRecord
{
    protected static string $resource = CurrenciesResource::class;

    protected ?string $maxContentWidth = '5xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Currency');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('auditInfo')
                ->label('Audit info')
                ->icon('heroicon-o-clipboard-document-list')
                ->modalHeading('Audit info')
                ->modalWidth('7xl')
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
