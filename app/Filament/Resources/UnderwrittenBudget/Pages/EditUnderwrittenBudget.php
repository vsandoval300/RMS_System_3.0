<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUnderwrittenBudget extends EditRecord
{
    protected static string $resource = UnderwrittenBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Budget entry updated')
            ->body('The budget entry has been updated successfully.');
    }

    public function getMaxContentWidth(): ?string
    {
        return '3xl';
    }
}
