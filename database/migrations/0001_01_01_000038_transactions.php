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
            //$table->engine('InnoDB');
            $table->uuid('id')->primary(); // UUID como string

            $table->integer('index'); 
            $table->decimal('proportion', 10, 6);
            $table->decimal('exch_rate', 18, 10);
            $table->date('due_date')->nullable();

            // remmitance_code (SET NULL si borran el código maestro)
            $table->string('remmitance_code', 14)->nullable(); // Permitir nulos aquí
            $table->foreign('remmitance_code')
                ->references('remmitance_code')->on('remmitance_codes')
                ->onDelete('set null'); // Añadir comportamiento al eliminar

            // relación con operative_docs (CASCADE al borrar el doc)
            $table->string('op_document_id', 19);
            $table->foreign('op_document_id')
                ->references('id')->on('operative_docs')
                ->cascadeOnDelete();
            
            $table->foreignId('transaction_type_id')->constrained('transaction_types');
            $table->foreignId('transaction_status_id')->constrained('transaction_statuses');
            $table->decimal('amount', 18, 2);
            
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
