<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_logs', function (Blueprint $table) {

            $table->char('id', 36)->primary();

            $table->char('transaction_id', 36);
            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('cascade');

            $table->integer('index');

            $table->unsignedBigInteger('deduction_type');
            $table->foreign('deduction_type')->references('id')->on('deductions');

            $table->unsignedBigInteger('from_entity');
            $table->foreign('from_entity')->references('id')->on('partners');

            $table->unsignedBigInteger('to_entity');
            $table->foreign('to_entity')->references('id')->on('partners');

            $table->date('sent_date')->nullable();
            $table->date('received_date')->nullable();

            $table->decimal('exch_rate', 18, 10);
            $table->decimal('proportion', 18, 6);              // ej: 0.25
            $table->decimal('commission_percentage', 18, 6);   // ej: 0.10
            $table->decimal('gross_amount', 18, 2);

            /**
             * base_calc = (gross_amount * proportion) / exch_rate
             */
            $grossCalcExpr = '((COALESCE(gross_amount,0) * COALESCE(proportion,0)) / NULLIF(COALESCE(exch_rate,0),0))';

            /**
             * commission_discount = base_calc * commission_percentage
             */
            $commissionExpr = "($grossCalcExpr * COALESCE(commission_percentage,0))";

            $table->decimal('gross_amount_calc', 18, 2)->storedAs($grossCalcExpr);
            $table->decimal('commission_discount', 18, 2)->storedAs($commissionExpr);

            $table->decimal('banking_fee', 18, 2)->default(0);

            /**
             * net_amount = base_calc - commission_discount - banking_fee
             * ⚠️ NO puede referenciar gross_amount_calc ni commission_discount en Postgres,
             * así que repetimos expresiones.
             */
            $table->decimal('net_amount', 18, 2)->storedAs(
                "(COALESCE($grossCalcExpr,0) - COALESCE($commissionExpr,0) - COALESCE(banking_fee,0))"
            );

            /**
             * STATUS como campo normal (ver nota abajo)
             */
            $table->string('status', 30)->storedAs(
                "CASE
                    WHEN received_date IS NOT NULL THEN 'Completed'
                    WHEN sent_date IS NOT NULL THEN 'In process'
                    ELSE 'Pending'
                END"
            );

            $table->string('evidence_path', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};
