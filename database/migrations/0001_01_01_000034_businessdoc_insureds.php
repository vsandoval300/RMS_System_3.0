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
            //$table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid
            
            // 🔗 Relación con operative_docs
            $table->string('op_document_id', 19);
            $table->foreign('op_document_id')
                ->references('id')
                ->on('operative_docs')
                ->cascadeOnDelete();

            // 🔗 Relación con cost_schemes
            // ✅ Relación (id es string(19), entonces aquí también)
            $table->string('cscheme_id', 19);
            $table->foreign('cscheme_id')
                ->references('id')
                ->on('cost_schemes')
                ->cascadeOnDelete();
            
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

