<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionsRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $transaction=new transaction();          $transaction->id = '4498540d-6d8f-4117-909c-e49d16c043bb';         $transaction->index = '1';         $transaction->remmitance_code = null;          $transaction->proportion = '1';         $transaction->exch_rate = '20.97';          $transaction->due_date = date('Y-m-d',strtotime('2021-01-05'));       $transaction->op_document_id = '2021-RIS0029-003-01';       $transaction->transaction_status_id = '3';        $transaction->transaction_type_id = '1';       $transaction->created_at = date('Y-m-d',strtotime('2021-10-26'));       $transaction->created_at = date('Y-m-d',strtotime('2021-10-26'));       $transaction->save(); 
    }
}
