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
         Schema::create('invoice_transactions', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid


            $table->uuid('invoice_id'); // Cambia a uuid
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            $table->uuid('transaction_code'); // Cambia a uuid
            $table->foreign('transaction_code')->references('id')->on('transactions')->onDelete('cascade');

            $table->foreignId('invoice_concept_id')->constrained('invoice_concepts')->cascadeOnDelete();
            $table->float('percentage'); // se pone el porcentaje de cobro relacionado al PA o el MA
            $table->float('discount'); // Se usa para registrar si hay algun decuento asociado
            
            $table->timestamps();
            $table->softDeletes();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_transactions');
    }
};
