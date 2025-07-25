<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cost_nodexes', function (Blueprint $table) {
            $table->renameColumn('cost_scheme_id', 'cscheme_id');
        });
    }

    public function down(): void
    {
        Schema::table('cost_nodexes', function (Blueprint $table) {
            $table->renameColumn('cscheme_id', 'cost_scheme_id');
        });
    }
};