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
            
            $table->integer('cns_reinsurer')->nullable();
            $table->string('name', 255);
            $table->string('short_name', 255);

            $table->unsignedBigInteger('parent_id')->nullable()->index(); // ✅ index agregado
            $table->foreign('parent_id')
                ->references('id')
                ->on('reinsurers');

            $table->string('acronym', 3);
            $table->string('class', 10);

            $table->text('logo')->nullable();
            $table->text('icon')->nullable();
            
            $table->integer('established');

            $table->foreignId('manager_id')
                ->constrained('managers')
                ->cascadeOnDelete();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->foreignId('reinsurer_type_id')
                ->constrained('reinsurer_types')
                ->cascadeOnDelete();

            $table->foreignId('operative_status_id')
                ->constrained('operative_statuses')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // 🔒 Unicidad solo entre registros vivos (deleted_at NULL)
            $table->unique(
                ['cns_reinsurer', 'deleted_at'],
                'reinsurers_cns_reinsurer_deleted_at_unique'
            );
            $table->unique(
                ['name', 'deleted_at'],
                'reinsurers_name_deleted_at_unique'
            );
            $table->unique(
                ['short_name', 'deleted_at'],
                'reinsurers_short_name_deleted_at_unique'
            );
            $table->unique(
                ['acronym', 'deleted_at'],
                'reinsurers_acronym_deleted_at_unique'
            );
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

    
	