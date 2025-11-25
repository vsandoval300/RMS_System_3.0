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
        Schema::create('regions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // 👇 sin index simple; vamos a usar unique compuesto
            $table->string('name', 30);
            $table->integer('region_code');

            $table->timestamps();
            $table->softDeletes();

            // 👇 Unicidad solo entre registros "vivos" (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'regions_name_deleted_at_unique');
            $table->unique(['region_code', 'deleted_at'], 'regions_region_code_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};



