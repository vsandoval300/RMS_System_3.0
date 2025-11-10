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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // 🔹 Relación polimórfica: cualquier modelo auditable
            $table->string('auditable_type');
            $table->string('auditable_id');        // auditable_type, auditable_id

            // 🔹 Usuario que hizo el cambio
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event');           // created, updated, deleted, etc.
            $table->json('changes')->nullable(); // { field: { old, new } }
            $table->timestamps();

            // 🔹 Aquí va el índice
            $table->index(['auditable_type', 'auditable_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
