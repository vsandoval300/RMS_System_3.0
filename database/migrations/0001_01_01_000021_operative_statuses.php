<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operative_statuses', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('acronym', 2); // ✔ Índice único para búsquedas rápidas
            $table->text('description');              // ✔ Descripción operativa

            $table->timestamps();
            $table->softDeletes();

            // 👇 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['acronym', 'deleted_at'], 'operative_statuses_acronym_deleted_at_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operative_statuses');
    }
};

