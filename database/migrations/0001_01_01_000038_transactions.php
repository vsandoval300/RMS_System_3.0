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
        Schema::create('transactions', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid

            $table->integer('index'); 
            $table->float('proportion');
            $table->float('exch_rate');
            $table->date('due_date')->nullable();

            $table->string('remmitance_code', 14)->nullable(); // Permitir nulos aquí
            $table->foreign('remmitance_code')->references('remmitance_code')->on('remmitance_codes')->onDelete('set null'); // Añadir comportamiento al eliminar
            $table->string('op_document_id', 19);
            $table->foreign('op_document_id')->references('id')->on('operative_docs');

            
            $table->foreignId('transaction_type_id')->constrained('transaction_types');
            $table->foreignId('transaction_status_id')->constrained('transaction_statuses');
    
            
            $table->timestamps();
            $table->softDeletes();
        });
        /**  Esta tabla considera los movimientos derivados de recepcion de primas y pagos de siniestros que van relacionados a una transacción
        * así como el registro de una reserva, es decir solo transacciones derivadas de un documento y que estrán organizadas por el campo 
        * remmitance_code
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
