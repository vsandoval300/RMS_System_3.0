<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_meetings', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->bigIncrements('id');

            $table->date('meeting_date')->index(); // 📅 se recomienda indexar si se listan por fecha
            $table->text('description');
            $table->text('document_path');

            // 👉 Relación opcional si cada reunión está asociada a un board
            // $table->foreignId('board_id')->constrained('boards')->cascadeOnDelete()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_meetings');
    }
};

