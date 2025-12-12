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
        Schema::create('treaty_docs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Índice interno del documento
            $table->unsignedInteger('index')->nullable();

            // Relación con treaties (PK string)
            $table->string('treaty_code', 19)->nullable();

            $table->foreign('treaty_code')
                ->references('treaty_code')
                ->on('treaties')
                ->nullOnDelete();   // si borras el treaty, pone treaty_code en NULL

            $table->text('description')->nullable();

            // Ruta en S3 u otro disco
            $table->string('document_path')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treaty_docs');
    }
};
