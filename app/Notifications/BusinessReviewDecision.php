<?php

namespace App\Notifications;

use App\Models\Business;
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
        return [
            'type'             => 'business_review_decision',
            'decision'         => $this->decision,
            'business_code'    => $this->business->business_code,
            'description'      => $this->business->description,
            'reviewed_by'      => $this->reviewerName,
            'revision_notes'   => $this->revisionNotes,
            'url'              => route('filament.admin.resources.businesses.edit', $this->business),
        ];
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
