<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionsAdamasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $transaction=new transaction();          $transaction->id = 'efa25a14-c570-4fde-8288-ad7fd204d7ec';         $transaction->index = '1';         $transaction->remmitance_code = null;          $transaction->proportion = '1';         $transaction->exch_rate = '1';          $transaction->due_date = date('Y-m-d',strtotime('2014-12-22'));       $transaction->op_document_id = '2014-ADA005-001-01';       $transaction->transaction_status_id = '3';        $transaction->transaction_type_id = '1';        $transaction->save(); 
        $transaction=new transaction();          $transaction->id = '63737071-bf00-4cef-8f65-01e28d8431aa';         $transaction->index = '1';         $transaction->remmitance_code = null;          $transaction->proportion = '1';         $transaction->exch_rate = '1';          $transaction->due_date = date('Y-m-d',strtotime('2015-04-15'));       $transaction->op_document_id = '2014-ADA005-002-01';       $transaction->transaction_status_id = '3';        $transaction->transaction_type_id = '1';        $transaction->save(); 
        $transaction=new transaction();          $transaction->id = '4001735d-f7ed-4718-85fd-d2de1c364bec';         $transaction->index = '1';         $transaction->remmitance_code = null;          $transaction->proportion = '1';         $transaction->exch_rate = '1';          $transaction->due_date = date('Y-m-d',strtotime('2015-04-15'));       $transaction->op_document_id = '2015-ADA005-001-01';       $transaction->transaction_status_id = '3';        $transaction->transaction_type_id = '1';        $transaction->save(); 
        $transaction=new transaction();          $transaction->id = '41bdb6b0-9fda-4a36-804e-0a2410f76a38';         $transaction->index = '1';         $transaction->remmitance_code = null;          $transaction->proportion = '1';         $transaction->exch_rate = '1';          $transaction->due_date = date('Y-m-d',strtotime('2015-09-01'));       $transaction->op_document_id = '2015-ADA005-002-01';       $transaction->transaction_status_id = '3';        $transaction->transaction_type_id = '1';        $transaction->save(); 
        $transaction=new transaction();          $transaction->id = '58e0ecae-0301-4a3f-9ada-07c6641a07ad';         $transaction->index = '1';         $transaction->remmitance_code = null;          $transaction->proportion = '1';         $transaction->exch_rate = '1';          $transaction->due_date = date('Y-m-d',strtotime('2015-12-22'));       $transaction->op_document_id = '2015-ADA005-003-01';       $transaction->transaction_status_id = '3';        $transaction->transaction_type_id = '1';        $transaction->save(); 
        $transaction=new transaction();          $transaction->id = '9316318c-8b69-4f6c-b62e-4b7a3bc16733';         $transaction->index = '1';         $transaction->remmitance_code = null;          $transaction->proportion = '1';         $transaction->exch_rate = '1';          $transaction->due_date = date('Y-m-d',strtotime('2015-05-04'));       $transaction->op_document_id = '2016-ADA005-001-01';       $transaction->transaction_status_id = '3';        $transaction->transaction_type_id = '1';        $transaction->save();  
    }
}
