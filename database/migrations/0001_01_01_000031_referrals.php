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
        Schema::create('referrals', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid

            $table->integer('index'); 
            $table->float('referral');
            $table->uuid('costnode_id'); // Cambia a uuid
            // Define la clave foránea referenciando costs_nodes
            $table->foreign('costnode_id')->references('id')->on('cost_nodes')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
