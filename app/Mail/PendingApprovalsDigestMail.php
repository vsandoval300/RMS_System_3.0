<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingApprovalsDigestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User       $manager,
        public readonly Collection $pendingBusinesses,
        public readonly Collection $pendingBatches,
    ) {}

    public function envelope(): Envelope
    {
        $total = $this->pendingBusinesses->count() + $this->pendingBatches->count();
        $week  = now()->format('d M Y');

        return new Envelope(
            subject: "[RMS] {$total} pending approval(s) — week of {$week}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.pending-approvals-digest',
        );
    }
}
