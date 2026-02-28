<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cost_schemes', function (Blueprint $table) {
            // agrega la columna (nullable por seguridad en data existente)
            $table->foreignId('created_by_user')
                ->nullable()
                ->after('description')
                ->constrained('users')
                ->nullOnDelete(); // si borran el user, deja null
        });
    }

    public function down(): void
    {
        Schema::table('cost_schemes', function (Blueprint $table) {
            $table->dropForeign(['created_by_user']);
            $table->dropColumn('created_by_user');
        });
    }
};
