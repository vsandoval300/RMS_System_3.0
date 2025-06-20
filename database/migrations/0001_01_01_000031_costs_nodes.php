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
        Schema::create('costs_nodes', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->uuid('id')->primary(); // Cambia a uuid

            $table->bigInteger('concept')->unsigned();
            $table->foreign('concept')->references('id')->on('deductions');

            $table->float('value');
            $table->foreignId('partner_id')->constrained('partners');
            $table->enum('referral_partner', ['Gatekeeper', 'Integrity', 'GMK-International'])->nullable();
            $table->foreignId('reinsurer_id')->constrained('reinsurers');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costs_nodes');
    }
};
