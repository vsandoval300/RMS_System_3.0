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

            $table->string('name', 150)->nullable();
            $table->string('short_name', 150)->index(); // ✅ indexed para búsquedas
            $table->text('description');
            $table->string('webpage')->nullable();      // ✅ mejor como string si no es muy largo
            $table->string('logo_path')->nullable();     // ✅ mejor como string si almacenas solo ruta
            
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->cascadeOnDelete();
                 

            $table->timestamps();
            $table->softDeletes();
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
