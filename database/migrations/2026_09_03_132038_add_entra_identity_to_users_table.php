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
        Schema::table('users', function (Blueprint $table) {
            $table->string('entra_tenant_id', 36)
                ->nullable()
                ->after('email');

            $table->string('entra_object_id', 36)
                ->nullable()
                ->after('entra_tenant_id');

            $table->unique(
                ['entra_tenant_id', 'entra_object_id'],
                'users_entra_identity_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_entra_identity_unique');

            $table->dropColumn([
                'entra_tenant_id',
                'entra_object_id',
            ]);
        });
    }
};
