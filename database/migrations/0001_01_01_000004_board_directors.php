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
        Schema::create('board_directors', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->foreignId('board_id')
                ->constrained('boards')
                ->cascadeOnDelete();

            $table->foreignId('director_id')
                ->constrained('directors')
                ->cascadeOnDelete();

            $table->unique(['board_id', 'director_id']); // Evita duplicados

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_directors');
    }
};

