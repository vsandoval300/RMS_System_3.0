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
       Schema::create('cost_scheme_nodes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Igual tipo que cost_schemes.id (string(19))
            $table->string('cscheme_id', 19);
            // SIN onDelete aquí (NO ACTION en SQL Server)
            $table->foreign('cscheme_id')
                ->references('id')->on('cost_schemes');

            // Igual tipo que cost_nodesx.id (string(21))
            $table->string('costnode_id', 21);
            // Sí cascade aquí
            $table->foreign('costnode_id')
                ->references('id')->on('cost_nodesx')
                ->cascadeOnDelete();

            $table->integer('index');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_scheme_nodes');
    }
};


