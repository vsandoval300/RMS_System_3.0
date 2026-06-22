<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_recalculations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('transaction_id');
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('recalculation_no')->default(1);

            $table->string('bordereaux_reference')->nullable();

            $table->decimal('reported_premium', 15, 2)->default(0);
            $table->decimal('reported_claims', 15, 2)->default(0);
            $table->decimal('previous_amount', 15, 2)->default(0);
            $table->decimal('new_amount', 15, 2)->default(0);

            $table->string('evidence_path')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_recalculations');
    }
};
