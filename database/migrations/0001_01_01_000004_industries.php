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
        Schema::create('industries', function (Blueprint $table) {
            $table->engine('InnoDB');

            $table->bigIncrements('id');

            $table->string('name', 100); // ✅ ya tiene índice implícito
            $table->text('description');           // ✅ bien como texto largo

            $table->timestamps();
            $table->softDeletes();

            // 👇 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'industries_name_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industries');
    }
};