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
        Schema::create('reinsurers', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->integer('cns_reinsurer')->nullable(); // Asegúrate de que este tipo de dato es el adecuado
            $table->string('name', 200);
            $table->string('short_name', 60);
            $table->unsignedBigInteger('parent_id')->nullable(); // Cambiado a unsignedBigInteger
            $table->foreign('parent_id')->references('id')->on('reinsurers'); // Agregado onDelete

            $table->string('acronym', 3);
            $table->string('class', 10);
            $table->text('logo')->nullable();
            $table->text('icon')->nullable();
            $table->integer('established');
            $table->foreignId('manager_id')->constrained('managers');
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('reinsurer_type_id')->constrained('reinsurer_types');
            $table->foreignId('operative_status_id')->constrained('operative_statuses');

            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reinsurers');
    }
};

    
	