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
        Schema::create('ubodocuments', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('document_path');
            $table->text('description');
            $table->date('issue_date');
            $table->date('expiration_date');
            $table->string('document_type');
            $table->foreignId('ubo_id')->constrained('ubos');
            $table->foreignId('ubo_doc_type_id')->constrained('ubo_doc_types');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubodocuments');
    }
};
