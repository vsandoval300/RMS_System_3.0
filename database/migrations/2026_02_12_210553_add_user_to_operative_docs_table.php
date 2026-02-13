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
            $table->bigInteger('created_by_user')->nullable()->after('af_mf');
            $table->foreign('created_by_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operative_docs', function (Blueprint $table) {
            $table->dropColumn('created_by_user');
        });
    }
};
