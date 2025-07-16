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
        // Habilitar IDENTITY_INSERT
//DB::statement('SET IDENTITY_INSERT boards ON');

        Schema::create('boards', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id'); // Columna de 
            $table->string('index'); // 
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Deshabilitar IDENTITY_INSERT
        //DB::statement('SET IDENTITY_INSERT boards OFF');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};