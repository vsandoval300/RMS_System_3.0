<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_status_history', function (Blueprint $table) {
            $table->id();
            $table->string('business_code', 19); // Relacionado al business
            $table->enum('previous_status', ['DFT', 'REV', 'RWK', 'APR']);
            $table->enum('new_status', ['DFT', 'REV', 'RWK', 'APR']);
            $table->unsignedBigInteger('changed_by_user_id');
            $table->timestamp('changed_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('comments')->nullable(); // opcional, por si quieres permitir comentarios
            $table->timestamps();

            $table->foreign('business_code')->references('business_code')->on('businesses');
            $table->foreign('changed_by_user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_status_history');
    }
};
