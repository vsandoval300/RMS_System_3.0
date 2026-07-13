<?php

namespace App\Notifications;

use App\Models\ImportBatch;
use Filament\Notifications\Actions\Action as FilamentAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BatchReviewDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ImportBatch $batch,
        public readonly string      $decision,      // 'approved' | 'rejected'
        public readonly string      $reviewerName,
        public readonly ?string     $notes = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // ── Bell (in-app) ──────────────────────────────────────────────────────────

    public function toDatabase(object $notifiable): array
    {
        $total = $this->batch->totalRecords();
        $url   = route('filament.admin.resources.import-batches.view', $this->batch);

        if ($this->decision === 'approved') {
            return FilamentNotification::make()
                ->title('Import Batch Approved')
                ->body("{$this->reviewerName} approved **{$this->batch->batch_code}** — {$total} records are now active")
                ->icon('heroicon-o-check-circle')
                ->success()
                ->actions([
                    FilamentAction::make('view')
                        ->label('View Batch')
                        ->url($url)
                        ->markAsRead(),
                ])
                ->getDatabaseMessage();
        }

        $body = "{$this->reviewerName} rejected **{$this->batch->batch_code}** — {$total} records were removed";
        if ($this->notes) {
            $body .= ": {$this->notes}";
        }

        return FilamentNotification::make()
            ->title('Import Batch Rejected')
            ->body($body)
            ->icon('heroicon-o-x-circle')
            ->danger()
            ->actions([
                FilamentAction::make('view')
                    ->label('View Batch')
                    ->url($url)
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    // ── Email (immediate — decision affects the importer directly) ─────────────

    public function toMail(object $notifiable): MailMessage
    {
        $url   = route('filament.admin.resources.import-batches.view', $this->batch);
        $total = $this->batch->totalRecords();

        if ($this->decision === 'approved') {
            return (new MailMessage)
                ->subject("Import Batch Approved: {$this->batch->batch_code}")
                ->greeting("Hello, {$notifiable->name}!")
                ->line("{$this->reviewerName} has approved your import batch:")
                ->line("**{$this->batch->batch_code}** — {$total} records are now active in the platform.")
                ->action('View Batch', $url);
        }

        $mail = (new MailMessage)
            ->subject("Import Batch Rejected: {$this->batch->batch_code}")
            ->greeting("Hello, {$notifiable->name}!")
            ->line("{$this->reviewerName} has rejected your import batch:")
            ->line("**{$this->batch->batch_code}** — {$total} records have been removed.");

        if ($this->notes) {
            $mail->line('**Reason:**')->line($this->notes);
        }

        return $mail->action('View Batch', $url)
                    ->line('You may correct the data and submit a new import if needed.');
    }
}
