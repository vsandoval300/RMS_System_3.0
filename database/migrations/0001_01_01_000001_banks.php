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
        Schema::create('banks', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->text('address');

            $table->string('aba_number', 9)->nullable();     // ✅ index si lo filtras o buscas
            $table->string('swift_code', 11);     // ✅ index si lo filtras o buscas

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(['name', 'deleted_at'], 'banks_name_deleted_at_unique');
            $table->unique(['aba_number', 'deleted_at'], 'banks_aba_number_deleted_at_unique');
            $table->unique(['swift_code', 'deleted_at'], 'banks_swift_code_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
