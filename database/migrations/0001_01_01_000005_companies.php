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
        Schema::create('companies', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name', 200);
            $table->string('acronym', 30);
            $table->text('activity');

            $table->foreignId('industry_id')
                ->constrained('industries')
                ->cascadeOnDelete();
                

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();
                
            $table->timestamps();
            $table->softDeletes();

            // (Opcional) Índice combinado si haces muchas búsquedas por país + industria
            // $table->index(['country_id', 'industry_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};


