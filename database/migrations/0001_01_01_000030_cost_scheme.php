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
        Schema::create('cost_schemes', function (Blueprint $table) {
            $table->engine('InnoDB');
            
            $table->string('id', 19)->primary();
            $table->integer('index'); 

            $table->float('share');
            $table->string('agreement_type',15);
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_schemes');
    }
};
