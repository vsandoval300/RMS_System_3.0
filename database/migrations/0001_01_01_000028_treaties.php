<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treaties', function (Blueprint $table) {

            // 🔥 Primary Key tipo string como en businesses
            $table->string('treaty_code', 19)->primary();

            // 👇 Nuevo: relación con reinsurers
            $table->foreignId('reinsurer_id')
                  ->constrained('reinsurers')
                  ->nullOnDelete();   

            $table->string('name');
            $table->string('contract_type')->nullable(); 
            $table->text('description')->nullable();
            

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treaties');
    }
};