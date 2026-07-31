<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUnderwrittenBudget extends CreateRecord
{
    protected static string $resource = UnderwrittenBudgetResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Budget entry created')
            ->body('The budget entry has been saved successfully.');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Save Budget Entry')
            ->modalDescription('Are you sure you want to save this budget entry? A new version will be created.')
            ->modalSubmitActionLabel('Save')
            ->action(function () {
                try {
                    $this->create();
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->unmountAction();
                    throw $e;
                }
            })
            ->keyBindings(['mod+s']);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            Action::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray')
                ->outlined(),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return '3xl';
    }
}
