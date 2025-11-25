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
        Schema::create('clients', function (Blueprint $table) {
            $table->engine('InnoDB');

            $table->bigIncrements('id');

            // 👇 alineados con el form (required + maxLength 255)
            $table->string('name', 255);
            $table->string('short_name', 255);

            $table->text('description');

            // required en el form + maxLength(255)
            $table->string('webpage', 255);

            // opcional: ruta del logo
            $table->string('logo_path', 255)->nullable();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'clients_name_deleted_at_unique');
            $table->unique(['short_name', 'deleted_at'], 'clients_short_name_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
