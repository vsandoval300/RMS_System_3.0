<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE cost_nodesx ALTER COLUMN id TYPE varchar(60)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cost_nodesx ALTER COLUMN id TYPE varchar(50)");
    }
};