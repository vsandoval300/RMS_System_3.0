<?php

namespace App\Notifications;

use App\Models\ImportBatch;
use Filament\Notifications\Actions\Action as FilamentAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BatchImportedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ImportBatch $batch,
        public readonly string      $importerName,
    ) {}

    public function via(object $notifiable): array
    {
        // Database only — email via weekly digest (SendPendingApprovalsDigest)
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $total = $this->batch->totalRecords();
        $url   = route('filament.admin.resources.import-batches.view', $this->batch);

        return FilamentNotification::make()
            ->title('Import Batch Pending Review')
            ->body("{$this->importerName} imported **{$this->batch->batch_code}** — {$total} records pending your approval")
            ->icon('heroicon-o-arrow-up-tray')
            ->warning()
            ->actions([
                FilamentAction::make('review')
                    ->label('Review Batch')
                    ->url($url)
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
