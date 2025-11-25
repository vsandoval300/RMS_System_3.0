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
        Schema::create('currencies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 70)->index();        // ✅ agrega índice si se busca o ordena
            $table->string('acronym', 3)->unique();     // ✅ ya está indexado por ser unique

            $table->timestamps();
            $table->softDeletes();

            // 👇 Unicidad solo entre registros "vivos" (deleted_at NULL),
            //    con nombres explícitos de índice
            $table->unique(['name', 'deleted_at'], 'currencies_name_deleted_at_unique');
            $table->unique(['acronym', 'deleted_at'], 'currencies_acronym_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
