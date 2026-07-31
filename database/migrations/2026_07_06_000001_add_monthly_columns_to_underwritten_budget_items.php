<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('underwritten_budget_items', function (Blueprint $table) {
            $table->decimal('m01', 18, 2)->default(0)->after('premium_budget');
            $table->decimal('m02', 18, 2)->default(0)->after('m01');
            $table->decimal('m03', 18, 2)->default(0)->after('m02');
            $table->decimal('m04', 18, 2)->default(0)->after('m03');
            $table->decimal('m05', 18, 2)->default(0)->after('m04');
            $table->decimal('m06', 18, 2)->default(0)->after('m05');
            $table->decimal('m07', 18, 2)->default(0)->after('m06');
            $table->decimal('m08', 18, 2)->default(0)->after('m07');
            $table->decimal('m09', 18, 2)->default(0)->after('m08');
            $table->decimal('m10', 18, 2)->default(0)->after('m09');
            $table->decimal('m11', 18, 2)->default(0)->after('m10');
            $table->decimal('m12', 18, 2)->default(0)->after('m11');
        });
    }

    public function down(): void
    {
        Schema::table('underwritten_budget_items', function (Blueprint $table) {
            $table->dropColumn(['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12']);
        });
    }
};
