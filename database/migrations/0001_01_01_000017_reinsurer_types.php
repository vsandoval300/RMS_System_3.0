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

            $table->string('type_acronym', 2);  // ✔ clave única corta
            $table->text('description');                  // ✔ descripción extendida

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(
                ['type_acronym', 'deleted_at'],
                'reinsurer_types_type_acronym_deleted_at_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reinsurer_types');
    }
};

