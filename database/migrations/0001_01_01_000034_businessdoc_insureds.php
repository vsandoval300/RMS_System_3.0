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
        Schema::create('businessdoc_insureds', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid
            
            //$table->foreignId('business_docs_id')->constrained('business_docs')->cascadeOnDelete();
            $table->string('biz_document_id', 19);
            $table->foreign('biz_document_id')->references('id')->on('business_docs')->onDelete('cascade');
            
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('coverage_id')->constrained('coverages');
            $table->float('premium');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businessdoc_insureds');
    }
};

