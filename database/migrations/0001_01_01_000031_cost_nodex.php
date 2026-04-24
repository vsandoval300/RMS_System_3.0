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
        Schema::create('cost_nodesx', function (Blueprint $table) {
            $table->engine('InnoDB');

            $table->string('id', 50)->primary();
            $table->integer('index'); 

            $table->bigInteger('concept')->unsigned();
            $table->foreign('concept')->references('id')->on('deductions');

            $table->float('value');

            // ✅ NUEVO CAMPO
            $table->boolean('apply_to_gross')->default(false);

            $table->foreignId('partner_source_id')->constrained('partners');
            $table->foreignId('partner_destination_id')->constrained('partners');
            
            $table->string('cscheme_id', 19); 
            $table->foreign('cscheme_id')
                  ->references('id')
                  ->on('cost_schemes')
                  ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_nodesx');
    }
};