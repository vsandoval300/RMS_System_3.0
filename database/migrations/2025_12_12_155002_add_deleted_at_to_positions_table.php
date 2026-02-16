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
        // ⚠️ MySQL no permite modificar ENUM fácilmente con Blueprint,
        // por eso usamos SQL directo.

        DB::statement("
            ALTER TABLE businesses 
            MODIFY claims_type 
            ENUM('Claims occurrence', 'Claims made', 'Hybrid')
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertimos al ENUM original
        DB::statement("
            ALTER TABLE businesses 
            MODIFY claims_type 
            ENUM('Claims occurrence', 'Claims made')
            NOT NULL
        ");
    }
};
