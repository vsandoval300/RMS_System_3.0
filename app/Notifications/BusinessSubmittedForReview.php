<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessSubmittedForReview extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Business $business,
        public readonly string   $submitterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // ── Bell (in-app) ──────────────────────────────────────────────────────────

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'             => 'business_submitted_for_review',
            'business_code'    => $this->business->business_code,
            'description'      => $this->business->description,
            'submitted_by'     => $this->submitterName,
            'url'              => route('filament.admin.resources.businesses.edit', $this->business),
        ];
    }

    // ── Email ──────────────────────────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('filament.admin.resources.businesses.edit', $this->business);

        return (new MailMessage)
            ->subject("Business Pending Review: {$this->business->business_code}")
            ->greeting("Hello, {$notifiable->name}!")
            ->line("{$this->submitterName} has submitted the following business for your review:")
            ->line("**{$this->business->business_code}** — {$this->business->description}")
            ->action('Review Business', $url)
            ->line('Please approve or request a revision at your earliest convenience.');
    }
}
