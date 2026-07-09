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
        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignId('reviewed_by_user_id')
                  ->nullable()
                  ->after('approval_status_updated_at')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('revision_notes')
                  ->nullable()
                  ->after('reviewed_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by_user_id']);
            $table->dropColumn(['reviewed_by_user_id', 'revision_notes']);
        });
    }
};
