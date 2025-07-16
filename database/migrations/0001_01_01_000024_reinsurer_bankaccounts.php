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
        Schema::create('reinsurer_bankaccounts', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->foreignId('reinsurer_id')->constrained('reinsurers')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            
            // ✅ Evita registros duplicados para la misma combinación
            $table->unique(['reinsurer_id', 'bank_account_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reinsurer_bankaccounts');
    }
};

