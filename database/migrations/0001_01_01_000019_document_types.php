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

            $table->string('name', 100)->unique();    // ✔ Nombre legible
            $table->string('acronym', 3)->unique();   // ✔ Clave de 3 letras
            $table->text('description');              // ✔ Descripción extendida

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
