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

            $table->string('name', 100)->unique();
            $table->text('address');

            $table->string('aba_number', 30)->index();     // ✅ index si lo filtras o buscas
            $table->string('swift_code', 30)->index();     // ✅ index si lo filtras o buscas

            $table->timestamps();
            $table->softDeletes();
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
