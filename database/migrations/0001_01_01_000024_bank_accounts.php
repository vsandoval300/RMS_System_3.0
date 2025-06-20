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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('beneficiary_acct_name',50)->nullable();
            $table->text('beneficiary_address')->nullable();
            $table->string('beneficiary_swift',20)->nullable();
            $table->string('beneficiary_acct_no',50)->nullable();
            $table->string('ffc_acct_name',300);
            $table->string('ffc_acct_no',30);
            $table->text('ffc_acct_address');
            $table->string('status_account',10);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('bank_id')->constrained('banks');
            $table->foreignId('intermediary_bank')->nullable()->constrained('banks');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};


    