<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions_supports', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->text('description');
            $table->string('support_path', 200)->nullable();

            // ✅ FK correcto (uuid -> uuid)
            $table->uuid('transaction_id');
            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions_supports');
    }
};
