<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * All businesses that existed before the approval workflow was introduced
     * are considered implicitly approved. This backfill promotes any record
     * that is not already APR / REJ / CAN to APR so dashboard stats are unaffected.
     */
    public function up(): void
    {
        DB::table('businesses')
            ->whereNotIn('approval_status', ['APR', 'REJ', 'CAN'])
            ->orWhereNull('approval_status')
            ->update(['approval_status' => 'APR']);
    }

    public function down(): void
    {
        // Not reversible — we cannot know which records had a non-APR status before.
    }
};
