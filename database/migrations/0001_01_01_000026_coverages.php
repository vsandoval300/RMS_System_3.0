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
        Schema::create('coverages', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            // 👇 alineados con el form
            $table->string('name', 255);
            $table->string('acronym', 20);

            $table->text('description');

            $table->foreignId('lob_id')
                ->constrained('line_of_businesses')
                ->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(
                ['name', 'deleted_at'],
                'coverages_name_deleted_at_unique'
            );

            $table->unique(
                ['acronym', 'deleted_at'],
                'coverages_acronym_deleted_at_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coverages');
    }
};
