<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('managers', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name', 255); // 🔍 Index si se filtra o se une con frecuencia
            $table->text('address');

            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'managers_name_deleted_at_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
