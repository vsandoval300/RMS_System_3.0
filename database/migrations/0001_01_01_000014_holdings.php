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
        Schema::create('holdings', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('name',200);
            $table->string('short_name',30);
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('client_id')->constrained('clients');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holdings');
    }
};


