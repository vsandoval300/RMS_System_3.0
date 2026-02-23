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
        Schema::table('operative_docs', function (Blueprint $table) {

            // 👇 Campo nuevo
            $table->date('rep_date')
                  ->nullable()
                  ->after('roe_fs'); 
                  // puedes cambiar la posición si prefieres
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operative_docs', function (Blueprint $table) {
            $table->dropColumn('rep_date');
        });
    }
};