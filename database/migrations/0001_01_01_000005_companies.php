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
        Schema::create('companies', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            // 👇 alineados con el form
            $table->string('name', 255);
            $table->string('acronym', 255);

            $table->text('activity');

            $table->foreignId('industry_id')
                ->constrained('industries')
                ->cascadeOnDelete();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();
                
            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(
                ['name', 'deleted_at'],
                'companies_name_deleted_at_unique'
            );
            $table->unique(
                ['acronym', 'deleted_at'],
                'companies_acronym_deleted_at_unique'
            );

            // (Opcional) Índice combinado si haces muchas búsquedas por país + industria
            // $table->index(['country_id', 'industry_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};


