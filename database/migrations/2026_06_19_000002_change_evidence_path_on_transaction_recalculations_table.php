<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Nullify values that are not valid JSON before casting
        DB::statement("UPDATE transaction_recalculations SET evidence_path = NULL WHERE evidence_path IS NOT NULL AND evidence_path !~ '^[\[{\"]'");
        DB::statement('ALTER TABLE transaction_recalculations ALTER COLUMN evidence_path TYPE json USING evidence_path::json');
        DB::statement('ALTER TABLE transaction_recalculations ALTER COLUMN evidence_path DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE transaction_recalculations ALTER COLUMN evidence_path TYPE varchar(255) USING evidence_path::text');
    }
};
