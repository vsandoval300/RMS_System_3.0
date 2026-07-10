<?php

namespace App\Notifications;

use App\Models\Business;
use Filament\Notifications\Actions\Action as FilamentAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessReviewDecision extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Business $business,
        public readonly string   $decision,      // 'approved' | 'revision'
        public readonly string   $reviewerName,
        public readonly ?string  $revisionNotes = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // ── Bell (in-app) ──────────────────────────────────────────────────────────

    public function toDatabase(object $notifiable): array
    {
        $url = route('filament.admin.resources.businesses.edit', $this->business);

        if ($this->decision === 'approved') {
            return FilamentNotification::make()
                ->title('Business Approved')
                ->body("{$this->reviewerName} approved **{$this->business->business_code}** — {$this->business->description}")
                ->icon('heroicon-o-check-circle')
                ->success()
                ->actions([
                    FilamentAction::make('view')
                        ->label('View Business')
                        ->url($url)
                        ->markAsRead(),
                ])
                ->getDatabaseMessage();
        }

        // decision === 'revision'
        $body = "{$this->reviewerName} requested a revision on **{$this->business->business_code}**";
        if ($this->revisionNotes) {
            $body .= ": {$this->revisionNotes}";
        }

        return FilamentNotification::make()
            ->title('Revision Required')
            ->body($body)
            ->icon('heroicon-o-arrow-path')
            ->danger()
            ->actions([
                FilamentAction::make('update')
                    ->label('Update Business')
                    ->url($url)
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    // ── Email ──────────────────────────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('filament.admin.resources.businesses.edit', $this->business);

        if ($this->decision === 'approved') {
            return (new MailMessage)
                ->subject("Business Approved: {$this->business->business_code}")
                ->greeting("Hello, {$notifiable->name}!")
                ->line("Great news! Your business has been approved by {$this->reviewerName}:")
                ->line("**{$this->business->business_code}** — {$this->business->description}")
                ->action('View Business', $url);
        }

        // decision === 'revision'
        $mail = (new MailMessage)
            ->subject("Revision Required: {$this->business->business_code}")
            ->greeting("Hello, {$notifiable->name}!")
            ->line("{$this->reviewerName} has requested a revision on your business:")
            ->line("**{$this->business->business_code}** — {$this->business->description}");

        if ($this->revisionNotes) {
            $mail->line("**Revision notes:**")
                 ->line($this->revisionNotes);
        }

        return $mail->action('Update Business', $url)
                    ->line('Please make the necessary changes and resubmit for review.');
    }
}
