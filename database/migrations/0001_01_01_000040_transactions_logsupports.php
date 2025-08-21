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
        //
        Schema::create('transactions_logsupports', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->char('id', 36)->primary(); // UUID como string

            $table->string('support_path',200)->nullable();

            $table->char('transaction_log_id',36); // Cambia a uuid
            $table->foreign('transaction_log_id')->references('id')->on('transaction_logs')->onDelete('cascade');


            $table->timestamps();
            $table->softDeletes();
        });
        /**
        * Esta tabla contempla el poder guardar los soportes de del log de la transacción como pueden ser comprobantes de pago, 
        * ya sean archivos pdf o imagenes en sus diferentes formatos pero principalmente en .png y .jpg
        */

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('transactions_logsupports');
    }
};