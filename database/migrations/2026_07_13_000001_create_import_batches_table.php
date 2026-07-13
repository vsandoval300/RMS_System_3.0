<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('batch_code', 20)->unique();

            $table->foreignId('imported_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', ['pending_review', 'approved', 'rejected'])->default('pending_review');

            $table->string('source_file_name')->nullable();
            $table->text('notes_importer')->nullable();
            $table->text('notes_reviewer')->nullable();

            $table->json('summary_json')->nullable();

            $table->timestamp('imported_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('imported_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
