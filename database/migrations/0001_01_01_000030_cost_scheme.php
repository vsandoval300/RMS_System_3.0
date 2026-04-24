<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_schemes', function (Blueprint $table) {
            $table->engine('InnoDB');

            $table->string('id', 19)->primary();
            $table->integer('index');

            $table->float('share');
            $table->string('agreement_type', 15);
            $table->text('description')->nullable();

            // ✅ NUEVO: usuario que crea el esquema
            $table->foreignId('created_by_user')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_schemes');
    }
};
