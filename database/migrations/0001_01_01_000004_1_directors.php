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
        Schema::create('directors', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            // 👇 alineados con el form
            $table->string('name', 200);
            $table->string('surname', 200)->index();    // sigues pudiendo filtrar por apellido
            $table->string('gender', 10)->index();

            // email opcional, pero único entre registros vivos
            $table->string('email', 255)->nullable();

            $table->string('phone', 40)->nullable();

            // address es required en el form → mejor no nullable y como text por si se alarga
            $table->text('address');

            // occupation required + maxLength(400)
            $table->string('occupation', 400)->index();

            // ruta de la imagen (S3)
            $table->string('image', 255)->nullable();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // 🔒 email único solo entre registros vivos (deleted_at NULL)
            $table->unique(
                ['email', 'deleted_at'],
                'directors_email_deleted_at_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directors');
    }
};

