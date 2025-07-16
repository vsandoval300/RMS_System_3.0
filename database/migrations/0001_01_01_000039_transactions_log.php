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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid


            $table->uuid('transaction_code'); // Cambia a uuid
            $table->foreign('transaction_code')->references('id')->on('transactions')->onDelete('cascade');

            $table->integer('index'); 

            $table->bigInteger('deduction_type')->unsigned();
            $table->foreign('deduction_type')->references('id')->on('deductions');

            $table->bigInteger('from_entity')->unsigned();
            $table->foreign('from_entity')->references('id')->on('partners');

            $table->bigInteger('to_entity')->unsigned();
            $table->foreign('to_entity')->references('id')->on('partners');

            $table->date('sent_date')->nullable();
            $table->date('received_date')->nullable();
            $table->float('exch_rate');
            $table->float('gross_amount');
            $table->float('commission_discount');
            $table->float('banking_fee');
            $table->float('net_amount');
            $table->enum('status',['Pending','Sent','Received','Completed']);
           
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};