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
        Schema::create('business_docs', function (Blueprint $table) {
            $table->engine('InnoDB');
            //$table->bigInteger('id')->unsigned()->primary();
            $table->string('id', 19)->primary();
            
            $table->foreignId('business_doc_type_id')->constrained('business_doc_types');
            $table->integer('index');
            $table->text('description');
            $table->date('inception_date');
            $table->date('expiration_date');
            $table->string('document_path',200)->nullable();
            $table->boolean('client_payment_tracking')->default(false); // ← Campo booleano agregado
            $table->float('roe');
            $table->string('business_code', 19);
            $table->foreign('business_code')->references('business_code')->on('businesses');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_docs');
    }
};


	