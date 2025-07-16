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
        Schema::create('businessdoc_schemes', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid
            
            $table->integer('index'); 
            
            $table->string('op_document_id', 19);
            $table->foreign('op_document_id')->references('id')->on('operative_docs')->onDelete('cascade');

            //$table->foreignId('cscheme_id')->constrained('cschemes')->cascadeOnDelete()

            $table->string('cscheme_id', 19); // Cambia a uuid
            $table->foreign('cscheme_id')->references('id')->on('cost_schemes')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businessdoc_schemes');
    }
};
