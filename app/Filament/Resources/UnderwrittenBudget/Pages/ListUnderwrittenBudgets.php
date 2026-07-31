<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListUnderwrittenBudgets extends ListRecords
{
    protected static string $resource = UnderwrittenBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new_version')
                ->label('New Version')
                ->icon('heroicon-m-document-duplicate')
                ->color('primary')
                ->url(UnderwrittenBudgetResource::getUrl('batch')),
        ];
    }
}
