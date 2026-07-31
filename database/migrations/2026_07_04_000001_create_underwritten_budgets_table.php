<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('underwritten_budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('reinsurer_id')
                ->constrained('reinsurers')
                ->cascadeOnDelete();

            // Año presupuestado (ej. 2026)
            $table->unsignedSmallInteger('year');

            // Número de versión auto-calculado por año (1 = inicial, 2 = primera revisión…)
            $table->unsignedTinyInteger('version')->default(1);

            // Etiqueta descriptiva de la versión
            $table->string('label', 100);

            // Monto de prima presupuestada en USD
            $table->decimal('premium_budget', 18, 2)->default(0);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Un reinsurer no puede tener dos veces la misma versión en el mismo año
            $table->unique(['reinsurer_id', 'year', 'version']);

            $table->index(['year', 'version']);
            $table->index('reinsurer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('underwritten_budgets');
    }
};
