<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'businesses',
        'cost_schemes',
        'cost_nodesx',
        'liability_structures',
        'operative_docs',
        'businessdoc_insureds',
        'businessdoc_schemes',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->uuid('import_batch_id')->nullable()->index();
                $table->foreign('import_batch_id')
                    ->references('id')
                    ->on('import_batches')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tbl) {
            Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                $table->dropForeign("{$tbl}_import_batch_id_foreign");
                $table->dropColumn('import_batch_id');
            });
        }
    }
};
