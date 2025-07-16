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
        Schema::create('remmitance_codes', function (Blueprint $table) {
            $table->engine('InnoDB');
            //$table->bigInteger('id')->unsigned();
            // Definir 'business_code' como la clave primaria
            $table->string('remmitance_code', 14)->primary();
            $table->bigInteger('id');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remmitance_codes');
    }
};


