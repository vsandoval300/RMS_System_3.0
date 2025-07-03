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
        $Transaction=new Transaction();          $Transaction->id = 'efa25a14-c570-4fde-8288-ad7fd204d7ec';         $Transaction->index = '1';         $Transaction->remmitance_code = null;          $Transaction->proportion = '1';         $Transaction->exch_rate = '1';          $Transaction->due_date = date('Y-m-d',strtotime('2014-12-22'));         $Transaction->Transaction_date = date('Y-m-d',strtotime('2015-02-23'));         $Transaction->banking_fee = '0';       $Transaction->biz_document_id = '2014-ADA005-001-01';       $Transaction->Transaction_status_id = '3';        $Transaction->Transaction_type_id = '1';        $Transaction->save(); 
        $Transaction=new Transaction();          $Transaction->id = '63737071-bf00-4cef-8f65-01e28d8431aa';         $Transaction->index = '1';         $Transaction->remmitance_code = null;          $Transaction->proportion = '1';         $Transaction->exch_rate = '1';          $Transaction->due_date = date('Y-m-d',strtotime('2015-04-15'));         $Transaction->Transaction_date = date('Y-m-d',strtotime('2015-05-15'));         $Transaction->banking_fee = '0';       $Transaction->biz_document_id = '2014-ADA005-002-01';       $Transaction->Transaction_status_id = '3';        $Transaction->Transaction_type_id = '1';        $Transaction->save(); 
        $Transaction=new Transaction();          $Transaction->id = '4001735d-f7ed-4718-85fd-d2de1c364bec';         $Transaction->index = '1';         $Transaction->remmitance_code = null;          $Transaction->proportion = '1';         $Transaction->exch_rate = '1';          $Transaction->due_date = date('Y-m-d',strtotime('2015-04-15'));         $Transaction->Transaction_date = date('Y-m-d',strtotime('2015-05-15'));         $Transaction->banking_fee = '0';       $Transaction->biz_document_id = '2015-ADA005-001-01';       $Transaction->Transaction_status_id = '3';        $Transaction->Transaction_type_id = '1';        $Transaction->save(); 
        $Transaction=new Transaction();          $Transaction->id = '41bdb6b0-9fda-4a36-804e-0a2410f76a38';         $Transaction->index = '1';         $Transaction->remmitance_code = null;          $Transaction->proportion = '1';         $Transaction->exch_rate = '1';          $Transaction->due_date = date('Y-m-d',strtotime('2015-09-01'));         $Transaction->Transaction_date = date('Y-m-d',strtotime('2015-10-01'));         $Transaction->banking_fee = '0';       $Transaction->biz_document_id = '2015-ADA005-002-01';       $Transaction->Transaction_status_id = '3';        $Transaction->Transaction_type_id = '1';        $Transaction->save(); 
        $Transaction=new Transaction();          $Transaction->id = '58e0ecae-0301-4a3f-9ada-07c6641a07ad';         $Transaction->index = '1';         $Transaction->remmitance_code = null;          $Transaction->proportion = '1';         $Transaction->exch_rate = '1';          $Transaction->due_date = date('Y-m-d',strtotime('2015-12-22'));         $Transaction->Transaction_date = date('Y-m-d',strtotime('2016-01-03'));         $Transaction->banking_fee = '0';       $Transaction->biz_document_id = '2015-ADA005-003-01';       $Transaction->Transaction_status_id = '3';        $Transaction->Transaction_type_id = '1';        $Transaction->save(); 
        $Transaction=new Transaction();          $Transaction->id = '9316318c-8b69-4f6c-b62e-4b7a3bc16733';         $Transaction->index = '1';         $Transaction->remmitance_code = null;          $Transaction->proportion = '1';         $Transaction->exch_rate = '1';          $Transaction->due_date = date('Y-m-d',strtotime('2015-05-04'));         $Transaction->Transaction_date = date('Y-m-d',strtotime('2016-05-06'));         $Transaction->banking_fee = '0';       $Transaction->biz_document_id = '2016-ADA005-001-01';       $Transaction->Transaction_status_id = '3';        $Transaction->Transaction_type_id = '1';        $Transaction->save(); 
    }
}
