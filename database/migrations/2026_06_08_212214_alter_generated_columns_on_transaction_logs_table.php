<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Guardar valores actuales
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN gross_amount_calc');
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN commission_discount');
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN net_amount');
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN status');

        // Recrear como columnas normales
        DB::statement('ALTER TABLE transaction_logs ADD COLUMN gross_amount_calc numeric(18,2) DEFAULT 0 NOT NULL');
        DB::statement('ALTER TABLE transaction_logs ADD COLUMN commission_discount numeric(18,2) DEFAULT 0 NOT NULL');
        DB::statement('ALTER TABLE transaction_logs ADD COLUMN net_amount numeric(18,2) DEFAULT 0 NOT NULL');
        DB::statement("ALTER TABLE transaction_logs ADD COLUMN status varchar(30) DEFAULT 'Pending' NOT NULL");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN gross_amount_calc');
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN commission_discount');
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN net_amount');
        DB::statement('ALTER TABLE transaction_logs DROP COLUMN status');

        $grossCalcExpr = '((COALESCE(gross_amount,0) * COALESCE(proportion,0)) / NULLIF(COALESCE(exch_rate,0),0))';
        $commissionExpr = "($grossCalcExpr * COALESCE(commission_percentage,0))";

        DB::statement("ALTER TABLE transaction_logs ADD COLUMN gross_amount_calc numeric(18,2) GENERATED ALWAYS AS ($grossCalcExpr) STORED");
        DB::statement("ALTER TABLE transaction_logs ADD COLUMN commission_discount numeric(18,2) GENERATED ALWAYS AS ($commissionExpr) STORED");
        DB::statement("ALTER TABLE transaction_logs ADD COLUMN net_amount numeric(18,2) GENERATED ALWAYS AS ((COALESCE($grossCalcExpr,0) - COALESCE($commissionExpr,0) - COALESCE(banking_fee,0))) STORED");

        DB::statement("
            ALTER TABLE transaction_logs ADD COLUMN status varchar(30)
            GENERATED ALWAYS AS (
                CASE
                    WHEN received_date IS NOT NULL THEN 'Completed'
                    WHEN sent_date IS NOT NULL THEN 'In process'
                    ELSE 'Pending'
                END
            ) STORED
        ");
    }
};