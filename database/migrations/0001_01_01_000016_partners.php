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
        Schema::create('partners', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('name',255);
            $table->string('short_name',255);
            $table->string('acronym',3);

            $table->foreignId('partner_types_id')
                ->constrained('partner_types')
                ->cascadeOnDelete();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // 👇 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'partners_name_deleted_at_unique');
            $table->unique(['short_name', 'deleted_at'], 'partners_short_name_deleted_at_unique');
            $table->unique(['acronym', 'deleted_at'], 'partners_acronym_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};

