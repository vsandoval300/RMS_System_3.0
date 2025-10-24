<?php

namespace Database\Seeders;

use App\Models\BusinessDocType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Businesses_doc_typesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '1. Slip ';
        $BusinessDocType->description ='Initial registration of the contract';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '2. Endorsement A – Complementary';
        $BusinessDocType->description ='Additional premium adjustment (increase or decrease)';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '3. Endorsement A – Modificatory';
        $BusinessDocType->description ='Full replacement or revaluation of the original premium';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '4. Endorsement B – No Premium Change';
        $BusinessDocType->description ='Informational or administrative change (no financial effect)';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '5. Endorsement C – Cancellation';
        $BusinessDocType->description ='Complete reversal or termination of the contract';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '6. Endorsement D – Partial Refund';
        $BusinessDocType->description ='Partial premium return due to reduced coverage or early termination';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '7. Endorsement E – Reinstatement';
        $BusinessDocType->description ='Coverage reinstatement after a loss, with additional premium';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '8. Endorsement F – Extension / Renewal';
        $BusinessDocType->description ='Extension of coverage period or renewal of policy terms';
        $BusinessDocType->save();

        $BusinessDocType = new BusinessDocType();
        $BusinessDocType->name = '9. Claim / Recovery';
        $BusinessDocType->description ='Loss or reimbursement transactions under the reinsurance contract';
        $BusinessDocType->save();
    }
}
