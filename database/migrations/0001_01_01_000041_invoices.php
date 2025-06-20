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
        Schema::create('invoices', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid

            $table->string('invoice_code',25);
            $table->date('issue_date');
            $table->date('expected_due_date');
            $table->text('description')->nullable();
            $table->text('invoice_path')->nullable();
            //$table->enum('status', ['Paid', 'Unpaid', 'Overdue'])->default('Unpaid');
            $table->foreignId('status_id')->constrained('invoice_statuses', 'id');
            $table->date('payment_date')->nullable();
            $table->float('amount');
            $table->float('exch_rate');

            $table->foreignId('currency_id')->constrained('currencies');
            $table->text('bill_to_id'); //Se desplageran los nombres como opciones de reaseguradores y partners
            $table->foreignId('issuer_id')->constrained('invoice_issuers');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
