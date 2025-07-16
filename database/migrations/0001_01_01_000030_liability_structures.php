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
        Schema::create('liability_structures', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->integer('index'); 
            $table->foreignId('coverage_id')->constrained('coverages');
            $table->boolean('cls')->default(false); 
            $table->float('limit');
            $table->text('limit_desc');
            $table->float('sublimit');
            $table->text('sublimit_desc');
            $table->float('deductible');
            $table->text('deductible_desc');
            $table->string('business_code', 19)->index();
            
            $table->foreign('business_code')
                  ->references('business_code')
                  ->on('businesses')
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
        Schema::dropIfExists('liability_structures');
    }
};
