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
        Schema::create('operative_docs', function (Blueprint $table) {
            $table->engine('InnoDB');

            //$table->bigInteger('id')->unsigned()->primary();
            $table->string('id', 19)->primary();
            
            $table->foreignId('operative_doc_type_id')->constrained('business_doc_types');
            $table->integer('index');
            $table->text('description');
            //$table->date('inception_date');
            //$table->date('expiration_date');
            $table->timestamp('inception_date');
            $table->timestamp('expiration_date');
            $table->string('document_path',200)->nullable();

            //$table->boolean('client_payment_tracking')->default(false);
            
            $table->string('business_code', 19)->index();
            
            $table->foreign('business_code')
                  ->references('business_code')
                  ->on('businesses')
                  ->onDelete('cascade');

            $table->float('af_mf');
            $table->float('roe_fs')->nullable();
            $table->date('rep_date')->nullable();

            // ✅ NUEVO: Campo agregado desde la segunda migración
            $table->foreignId('created_by_user')->nullable()->constrained('users')->cascadeOnDelete();

            

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ✅ NUEVO: Drop FK antes de eliminar tabla (mejor práctica)
        Schema::table('operative_docs', function (Blueprint $table) {
            $table->dropForeign(['created_by_user']);
        });

        Schema::dropIfExists('operative_docs');
    }
};

	