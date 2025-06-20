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
        Schema::create('ubos', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');
            
            $table->string('name',100);
            $table->string('surname',100);
            $table->string('acronym',100);
            $table->string('gender',10);
            $table->date('birth_date');
            $table->string('email',100);
            $table->string('phone',20);
            $table->string('address',200);
            $table->string('occupation',200);
            $table->string('identity',200);
            $table->string('passport_num',200);
            $table->text('qualification');
            $table->string('image',200);
            $table->foreignId('birthplace')->constrained('countries')->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubos');
    }
};
