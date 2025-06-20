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
        Schema::create('cscheme_cnodes', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid
            
            $table->string('cscheme_id', 19); // Cambia a uuid
            $table->foreign('cscheme_id')->references('id')->on('cschemes')->onDelete('cascade');
            
            $table->uuid('costnode_id'); // Cambia a uuid
            $table->foreign('costnode_id')->references('id')->on('costs_nodes')->onDelete('cascade');
            $table->integer('index');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cscheme_cnodes');
    }
};
