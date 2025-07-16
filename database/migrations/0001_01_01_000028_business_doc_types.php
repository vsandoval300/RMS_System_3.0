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
       Schema::create('business_doc_types', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('name', 200)->unique(); // ← Protege contra duplicados
            $table->text('description');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_doc_types');
    }
};
