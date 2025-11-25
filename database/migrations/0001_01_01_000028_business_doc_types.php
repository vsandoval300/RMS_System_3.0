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
            
            // 👇 alineado con el form
            $table->string('name', 255);
            $table->text('description');

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(
                ['name', 'deleted_at'],
                'business_doc_types_name_deleted_at_unique'
            );
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
