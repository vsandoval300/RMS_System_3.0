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
        Schema::create('countries', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name',150);
            $table->string('alpha_2',2);
            $table->string('alpha_3',3);
            $table->string('country_code',3);
            $table->string('iso_code',30);

            $table->string('am_best_code',10)->index();   // ✅ Index si haces filtros/búsquedas
            $table->float('latitude')->index();           // Opcional: si filtras o haces geobúsquedas
            $table->float('longitude')->index();          // Opcional: idem arriba

            $table->foreignId('region_id')
                  ->constrained('regions')
                  ->cascadeOnDelete()
                  ->index()
                  ->name('fk_countries_region_id'); // 👈 esto evita conflictos de nombres

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'countries_name_deleted_at_unique');
            $table->unique(['alpha_2', 'deleted_at'], 'countries_alpha2_deleted_at_unique');
            $table->unique(['alpha_3', 'deleted_at'], 'countries_alpha3_deleted_at_unique');
            $table->unique(['country_code', 'deleted_at'], 'countries_country_code_deleted_at_unique');
            $table->unique(['iso_code', 'deleted_at'], 'countries_iso_code_deleted_at_unique');

            // Si de verdad quieres latitude/longitude únicos:
            $table->unique(['latitude', 'deleted_at'], 'countries_latitude_deleted_at_unique');
            $table->unique(['longitude', 'deleted_at'], 'countries_longitude_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};



