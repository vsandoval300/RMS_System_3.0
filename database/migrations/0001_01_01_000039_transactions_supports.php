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
        Schema::create('transactions_supports', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->char('id', 36)->primary(); // UUID como string
            
            $table->text('description');
            $table->string('support_path',200)->nullable();
            
            $table->char('transaction_code', 36);
            $table->foreign('transaction_code')->references('id')->on('transactions')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
        /* Esta tabla contempla los soportes que se puedan egenerar derivados de una transaccón como borderoux
         de excel o bien archivos pdf con estados de cuenta que van ligados a una transacción */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions_supports');
    }
};
