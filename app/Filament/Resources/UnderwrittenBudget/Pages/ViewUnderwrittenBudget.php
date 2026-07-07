<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUnderwrittenBudget extends ViewRecord
{
    protected static string $resource = UnderwrittenBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return '3xl';
    }
}
