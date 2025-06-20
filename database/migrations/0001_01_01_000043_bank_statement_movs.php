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
        /* Schema::create('bank_statement_movs', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->date('movement_date');
            $table->float('amount');
            $table->foreignId('bank_statement_id')->constrained('bank_statements')->cascadeOnDelete();
            $table->string('transaction_code', 8)->nullable();
            $table->foreign('transaction_code')->references('transaction_code')->on('transactions')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); 
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_movs');
    }
};
