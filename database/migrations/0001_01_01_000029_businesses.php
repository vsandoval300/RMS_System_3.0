<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Incluir la clase DB

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->engine('InnoDB');

            // Definir 'business_code' como la clave primaria
            $table->string('business_code', 19)->primary();
            
            $table->integer('index'); 
            $table->text('description');
            $table->enum('reinsurance_type', ['Facultative', 'Treaty']);
            $table->enum('risk_covered', ['Live', 'Non-Life']);
            $table->enum('business_type', ['Own', 'Third party']);
            $table->enum('premium_type', ['Fixed', 'Estimated']);
            $table->enum('purpose', ['Strategic', 'Normal']);
            $table->enum('claims_type', ['Claims occurrence', 'Claims made']);

            // Claves foráneas a otras tablas
            $table->foreignId('reinsurer_id')->constrained('reinsurers');
            $table->string('parent_id', 19)->nullable();
            $table->string('renewed_from_id', 19)->nullable();
            $table->foreignId('producer_id')->constrained('partners');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('region_id')->constrained('regions');

            // CICLO DE APROBACIÓN
            $table->enum('approval_status', [
                'DFT',         // Draft
                'PND',         // Pending Approval
                'APR',         // Approved
                'REJ',         // Rejected
                'CAN',         // Cancelled
            ])->default('DFT');
            $table->timestamp('approval_status_updated_at')->nullable();

            // CICLO DE VIDA DEL NEGOCIO
            $table->enum('business_lifecycle_status', [
                'On Hold',
                'In Force',
                'To Expire',
                'Expired',
                'Cancelled',
            ])->default('On Hold');
            $table->timestamp('business_lifecycle_status_updated_at')->nullable();
            

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_underwritten_year_and_month');
        Schema::dropIfExists('businesses');
    }
};