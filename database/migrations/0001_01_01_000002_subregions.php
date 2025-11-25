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
        Schema::create('subregions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 60)->index();             // ✅ index si lo buscas o filtras
            $table->integer('subregion_code')->index();      // ✅ index si lo filtras
            $table->foreignId('region_id')
                ->constrained('regions')
                ->cascadeOnDelete()
                ->index();                                 // ✅ asegura índice en la FK

            $table->timestamps();
            $table->softDeletes();

            // 👇 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'subregions_name_deleted_at_unique');
            $table->unique(['subregion_code', 'deleted_at'], 'subregions_subregion_code_deleted_at_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subregions'); // ✅ corregido
    }
};


