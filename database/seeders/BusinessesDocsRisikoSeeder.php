<?php

namespace Database\Seeders;

use App\Models\OperativeDoc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessesDocsRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $OperativeDoc=new OperativeDoc();          $OperativeDoc->id = '2021-RIS0029-003-01';         $OperativeDoc->operative_doc_type_id = '1';        $OperativeDoc->index = '1';         $OperativeDoc->description = 'Facultative Non-Proportional Reinsurance Contract';         $OperativeDoc->inception_date = date('Y-m-d',strtotime('2021-10-26'));         $OperativeDoc->expiration_date = date('Y-m-d',strtotime('2022-10-26'));         $OperativeDoc->document_path =  'reinsurers/business_documents/2021-RIS0029-003-01.pdf' ;               $OperativeDoc->af_mf =  '0' ;                             $OperativeDoc->business_code = '2021-RIS0029-003';          $OperativeDoc->created_at = date('Y-m-d',strtotime('2021-10-26'));         $OperativeDoc->updated_at = date('Y-m-d',strtotime('2022-10-26'));         $OperativeDoc->save();  
    }
}
