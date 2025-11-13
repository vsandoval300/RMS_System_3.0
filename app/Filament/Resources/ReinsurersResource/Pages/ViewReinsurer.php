<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms;

class ViewReinsurer extends ViewRecord
{
    protected static string $resource = ReinsurersResource::class;

    public function getContentTabLabel(): ?string
    {
        return 'Reinsurer Details';
    }

    protected ?string $maxContentWidth = '7xl';

    public function getTitle(): string
    {
        return 'View – ' . ($this->record?->name ?? 'Reinsurer');
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
