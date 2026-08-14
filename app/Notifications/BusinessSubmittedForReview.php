<?php

namespace App\Notifications;

use App\Models\Business;
use App\Services\TeamsNotificationService;
use Filament\Actions\Action as FilamentAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class BusinessSubmittedForReview extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Business $business,
        public readonly string   $submitterName,
    ) {}

    public function via(object $notifiable): array
    {
        // Mail moved to weekly digest — SendPendingApprovalsDigest command
        return ['database'];
    }

    // ── Bell (in-app) ──────────────────────────────────────────────────────────

    public function toDatabase(object $notifiable): array
    {

        $managerEmail = $this->business->user?->manager?->email;

        if ($managerEmail) {
            app(TeamsNotificationService::class)->businessSubmitted(
                recipient: $managerEmail,
                businessCode: $this->business->business_code,
                description: $this->business->description,
                submitterName: $this->submitterName,
                reviewUrl: route(
                    'filament.admin.resources.businesses.edit',
                    $this->business
                ),
            );
        }
        
        
        return FilamentNotification::make()
            ->title('Business Submitted for Review')
            ->body("{$this->submitterName} submitted **{$this->business->business_code}** — {$this->business->description}")
            ->icon('heroicon-o-paper-airplane')
            ->warning()
            ->actions([
                FilamentAction::make('review')
                    ->label('Review Business')
                    ->url(route('filament.admin.resources.businesses.edit', $this->business))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
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
