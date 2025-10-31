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
        Schema::create('line_of_businesses', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('name',100)->unique();
            $table->text('description'); 
            $table->string('risk_covered',100);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_of_businesses');
    }
};
