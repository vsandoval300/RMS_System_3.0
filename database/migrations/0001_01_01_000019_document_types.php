<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name', 255);    // ✔ Nombre legible
            $table->string('acronym', 2);   // ✔ Clave de 3 letras
            $table->text('description');              // ✔ Descripción extendida

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'document_types_name_deleted_at_unique');
            $table->unique(['acronym', 'deleted_at'], 'document_types_acronym_deleted_at_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
