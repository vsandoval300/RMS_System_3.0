<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cost_nodesx', function (Blueprint $table) {
            $table->boolean('apply_to_gross')
                  ->default(false)
                  ->after('value'); // Lo colocamos después de value
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_nodesx', function (Blueprint $table) {
            $table->dropColumn('apply_to_gross');
        });
    }
};