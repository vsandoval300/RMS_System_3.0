<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old denormalized table (dev environment — no prod data)
        Schema::dropIfExists('underwritten_budgets');

        // Header: one row per budget version (year + version number)
        Schema::create('underwritten_budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('version')->default(1);

            $table->string('label', 100);
            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Each year+version is unique (a version covers all reinsurers at once)
            $table->unique(['year', 'version']);
            $table->index('year');
        });

        // Line items: one row per reinsurer inside a budget version
        Schema::create('underwritten_budget_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('budget_id');
            $table->foreign('budget_id')
                ->references('id')
                ->on('underwritten_budgets')
                ->cascadeOnDelete();

            $table->foreignId('reinsurer_id')
                ->constrained('reinsurers')
                ->cascadeOnDelete();

            $table->decimal('premium_budget', 18, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Each reinsurer appears once per budget version
            $table->unique(['budget_id', 'reinsurer_id']);
            $table->index('budget_id');
            $table->index('reinsurer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('underwritten_budget_items');
        Schema::dropIfExists('underwritten_budgets');

        // Restore original denormalized table
        Schema::create('underwritten_budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('reinsurer_id')->constrained('reinsurers')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('version')->default(1);
            $table->string('label', 100);
            $table->decimal('premium_budget', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['reinsurer_id', 'year', 'version']);
            $table->index(['year', 'version']);
            $table->index('reinsurer_id');
        });
    }
};
