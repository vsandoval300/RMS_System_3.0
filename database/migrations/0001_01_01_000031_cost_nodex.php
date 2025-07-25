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

            // Definir 'business_code' como la clave primaria
            $table->string('id', 21)->primary();
            $table->integer('index'); 

            $table->bigInteger('concept')->unsigned();
            $table->foreign('concept')->references('id')->on('deductions');

            $table->float('value');
            $table->foreignId('partner_id')->constrained('partners');
            $table->enum('referral_partner', ['Gatekeeper', 'Integrity', 'GMK-International'])->nullable();
            $table->string('cscheme_id', 19); 
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
        Schema::dropIfExists('cost_nodesx');
    }
};
