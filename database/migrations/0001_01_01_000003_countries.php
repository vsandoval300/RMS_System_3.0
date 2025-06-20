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
        Schema::create('countries', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->string('name',150)->unique();
            $table->string('alpha_2',2)->unique();
            $table->string('alpha_3',3)->unique();
            $table->string('country_code',3)->unique();
            $table->string('iso_code',30)->unique();
            $table->string('am_best_code',10);
            $table->float('latitude');
            $table->float('longitude');
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};



