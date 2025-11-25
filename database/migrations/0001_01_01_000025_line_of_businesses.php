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
            
            $table->string('name',255);
            $table->text('description'); 
            $table->string('risk_covered',20);
            
            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(
                ['name', 'deleted_at'],
                'lobs_name_deleted_at_unique'
            );
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
