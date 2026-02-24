<?php

namespace Database\Seeders;

use App\Models\OperativeDoc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessesDocsMesoamericaSeeder2026 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $OperativeDoc=new OperativeDoc();          $OperativeDoc->id = '2026-MES022-001-01';         $OperativeDoc->operative_doc_type_id = '1';        $OperativeDoc->index = '1';         $OperativeDoc->description = 'Facultative Reinsurance for General Third-Party Liability';         $OperativeDoc->inception_date = date('Y-m-d',strtotime('2026-01-31'));         $OperativeDoc->expiration_date = date('Y-m-d',strtotime('2027-01-31'));         $OperativeDoc->document_path =  'reinsurers/business_documents/2026-MES022-001-01.pdf' ;               $OperativeDoc->af_mf =  '0' ;                                $OperativeDoc->roe_fs =  '1' ;                              $OperativeDoc->created_by_user =  '15' ;                              $OperativeDoc->business_code = '2026-MES022-001';          $OperativeDoc->created_at = date('Y-m-d',strtotime('2026-02-01'));         $OperativeDoc->updated_at = date('Y-m-d',strtotime(''));         $OperativeDoc->save(); 
        $OperativeDoc=new OperativeDoc();          $OperativeDoc->id = '2026-MES022-001-02';         $OperativeDoc->operative_doc_type_id = '4';        $OperativeDoc->index = '2';         $OperativeDoc->description = 'Facultative Reinsurance for General Third-Party Liability';         $OperativeDoc->inception_date = date('Y-m-d',strtotime('2026-01-31'));         $OperativeDoc->expiration_date = date('Y-m-d',strtotime('2027-01-31'));         $OperativeDoc->document_path =  'reinsurers/business_documents/2026-MES022-001-02.pdf' ;               $OperativeDoc->af_mf =  '0' ;                                $OperativeDoc->roe_fs =  '1' ;                              $OperativeDoc->created_by_user =  '15' ;                              $OperativeDoc->business_code = '2026-MES022-001';          $OperativeDoc->created_at = date('Y-m-d',strtotime('2026-02-01'));         $OperativeDoc->updated_at = date('Y-m-d',strtotime(''));         $OperativeDoc->save();          

    }
}