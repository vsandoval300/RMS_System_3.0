<?php

namespace App\Console\Commands;

use App\Mail\PendingApprovalsDigestMail;
use App\Models\Business;
use App\Models\ImportBatch;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPendingApprovalsDigest extends Command
{
    protected $signature   = 'rms:send-approvals-digest';
    protected $description = 'Send weekly digest email to managers with pending business/batch approvals';

    public function handle(): int
    {
        // ── Camino 1: businesses waiting for manual approval ───────────────────
        $pendingBusinessesByManager = Business::withoutGlobalScopes()
            ->where('approval_status', 'PND')
            ->whereNull('deleted_at')
            ->with(['createdByUser'])
            ->get()
            ->filter(fn ($b) => $b->createdByUser?->manager_id)
            ->groupBy(fn ($b) => $b->createdByUser->manager_id);

        // ── Camino 2: import batches waiting for approval ──────────────────────
        $pendingBatchesByManager = ImportBatch::where('status', 'pending_review')
            ->with(['importer'])
            ->get()
            ->filter(fn ($b) => $b->importer?->manager_id)
            ->groupBy(fn ($b) => $b->importer->manager_id);

        // ── Union of manager IDs from both paths ───────────────────────────────
        $managerIds = collect($pendingBusinessesByManager->keys())
            ->merge($pendingBatchesByManager->keys())
            ->unique();

        if ($managerIds->isEmpty()) {
            $this->info('No pending approvals found. No emails sent.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($managerIds as $managerId) {
            $manager = User::find($managerId);

            if (! $manager?->email) {
                continue;
            }

            $businesses = $pendingBusinessesByManager->get($managerId, collect());
            $batches    = $pendingBatchesByManager->get($managerId, collect());

            Mail::to($manager)->send(
                new PendingApprovalsDigestMail($manager, $businesses, $batches)
            );

            $bizCount   = $businesses->count();
            $batchCount = $batches->count();
            $this->line("  Sent to {$manager->name} ({$manager->email}) — {$bizCount} business(es), {$batchCount} batch(es)");
            $sent++;
        }

        $this->info("Digest sent to {$sent} manager(s).");

        return self::SUCCESS;
    }
}
