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
        Schema::create('business_units', function (Blueprint $table) {
            $table->engine('InnoDB');

            $table->bigIncrements('id');

            $table->string('name', 60);   // ✅ índice implícito por unique
            $table->text('description');

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // 👇 Unicidad solo entre registros vivos
            $table->unique(['name', 'deleted_at'], 'business_units_name_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_units');
    }
};

