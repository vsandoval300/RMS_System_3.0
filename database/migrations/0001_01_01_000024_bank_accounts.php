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
            
            // 👇 alineado con el form (required + maxLength 255)
            $table->string('beneficiary_acct_name', 255)->nullable();
            $table->text('beneficiary_address')->nullable();

            // SWIFT: 8 u 11 chars, lo limitamos a 11; opcional en el form
            $table->string('beneficiary_swift', 11)->nullable();

            // required en el form, hasta 255
            $table->string('beneficiary_acct_no', 255)->nullable();

            // For Further Account
            $table->string('ffc_acct_name', 255);
            $table->string('ffc_acct_no', 255);
            $table->text('ffc_acct_address');

            $table->string('status_account', 10);

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();

            $table->foreignId('bank_id')
                ->constrained('banks')
                ->cascadeOnDelete();

            // 👇 columna tal cual la tienes, como FK a banks
            $table->foreignId('intermediary_bank')
                ->nullable()
                ->constrained('banks')
                ->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre cuentas vivas (deleted_at NULL)
            $table->unique(
                ['ffc_acct_no', 'deleted_at'],
                'bank_accounts_ffc_acct_no_deleted_at_unique'
            );
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


