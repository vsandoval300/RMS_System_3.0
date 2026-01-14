<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions_logsupports', function (Blueprint $table) {
            

            $table->uuid('id')->primary();

            $table->string('support_path', 200)->nullable();

            // ✅ FK compatible con transaction_logs.id (uuid)
            $table->uuid('transaction_log_id');
            $table->foreign('transaction_log_id')
                ->references('id')->on('transaction_logs')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions_logsupports');
    }
};
