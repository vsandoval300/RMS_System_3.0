<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_transactions', function (Blueprint $table) {

            $table->uuid('id')->primary();

            // invoices.id es CHAR(36)
            $table->char('invoice_id', 36);
            $table->foreign('invoice_id')
                ->references('id')->on('invoices')
                ->cascadeOnDelete();

            // transactions.id es UUID
            $table->uuid('transaction_code');
            $table->foreign('transaction_code')
                ->references('id')->on('transactions')
                ->cascadeOnDelete();

            $table->foreignId('invoice_concept_id')
                ->constrained('invoice_concepts')
                ->cascadeOnDelete();

            $table->float('percentage');
            $table->float('discount');

            $table->timestamps();
            $table->softDeletes();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_transactions');
    }
};
