<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Convierte timestamptz -> timestamp SIN TZ preservando hora local (con DST)
        /* DB::statement("
            ALTER TABLE operative_docs
            ALTER COLUMN inception_date TYPE timestamp without time zone
            USING inception_date AT TIME ZONE 'America/Mexico_City'
        ");

        DB::statement("
            ALTER TABLE operative_docs
            ALTER COLUMN expiration_date TYPE timestamp without time zone
            USING expiration_date AT TIME ZONE 'America/Mexico_City'
        "); */
    }

    public function down(): void
    {
        // Revierte timestamp -> timestamptz interpretando los valores como hora local CDMX
        /* DB::statement("
            ALTER TABLE operative_docs
            ALTER COLUMN inception_date TYPE timestamp with time zone
            USING inception_date AT TIME ZONE 'America/Mexico_City'
        ");

        DB::statement("
            ALTER TABLE operative_docs
            ALTER COLUMN expiration_date TYPE timestamp with time zone
            USING expiration_date AT TIME ZONE 'America/Mexico_City'
        "); */
    }
};