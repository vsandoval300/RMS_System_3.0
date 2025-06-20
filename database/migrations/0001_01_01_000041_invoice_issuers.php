<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_issuers', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('name', 200);
            $table->string('short_name', 60);
            $table->string('acronym', 3);
            $table->foreignId('country_id')->constrained('countries');
            $table->string('address', 300);
            
            $table->foreignId('bankaccount_id')->nullable()->constrained('bank_accounts');
            $table->timestamps();
            $table->softDeletes();
        });
        // Agregar comentario a nivel de tabla
        //DB::statement("ALTER TABLE invoice_issuers COMMENT = 'Table to store information about income beneficiaries';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_issuers');
        //DB::statement("ALTER TABLE `revenue_invoice_issuers` COMMENT ''"); // Opcional: Elimina el comentario si es necesario.
    }
};

