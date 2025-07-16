<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reinsurer_types', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('type_acronym', 5)->unique();  // ✔ clave única corta
            $table->text('description');                  // ✔ descripción extendida

            $table->timestamps();
            $table->softDeletes();

            // Opcional: índice fulltext si haces búsquedas de texto completo en descripción
            // $table->fullText('description');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reinsurer_types');
    }
};

