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
        Schema::create('directors', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name', 100);
            $table->string('surname', 100)->index();   // ✅ Index si filtras por apellido
            $table->string('gender', 10)->index();     // ✅ Index si haces filtros
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('occupation', 200)->index(); // ✅ Index si haces filtros por ocupación
            $table->string('image', 200)->nullable();

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
        Schema::dropIfExists('directors');
    }
};

