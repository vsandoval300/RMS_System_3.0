<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_types', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name', 100)->unique();    // 🔒 Unicidad explícita
            $table->text('description');
            $table->string('acronym', 10)->unique();  // 🔒 Asegura códigos únicos

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_types');
    }
};

