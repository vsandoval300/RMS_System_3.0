<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE clients ALTER COLUMN webpage DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE clients SET webpage = '' WHERE webpage IS NULL");
        DB::statement('ALTER TABLE clients ALTER COLUMN webpage SET NOT NULL');
    }
};
