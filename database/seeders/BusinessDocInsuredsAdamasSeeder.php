<?php

namespace Database\Seeders;

use App\Models\businessdoc_insured;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessDocInsuredsAdamasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $businessdoc_insured=new businessdoc_insured();          $businessdoc_insured->id = 'f2707322-e188-42cc-bd53-fd2b56d05e3e';        $businessdoc_insured->biz_document_id = '2014-ADA005-001-01';         $businessdoc_insured->company_id = '39';         $businessdoc_insured->coverage_id = '40';          $businessdoc_insured->premium = '4867500.15';         $businessdoc_insured->save(); 
        $businessdoc_insured=new businessdoc_insured();          $businessdoc_insured->id = '34210fe2-480b-4021-aa7a-780e72258a81';        $businessdoc_insured->biz_document_id = '2014-ADA005-002-01';         $businessdoc_insured->company_id = '39';         $businessdoc_insured->coverage_id = '40';          $businessdoc_insured->premium = '3035381.85';         $businessdoc_insured->save(); 
        $businessdoc_insured=new businessdoc_insured();          $businessdoc_insured->id = 'fd5a7d17-772b-475e-8c44-07ae14b42f87';        $businessdoc_insured->biz_document_id = '2015-ADA005-001-01';         $businessdoc_insured->company_id = '39';         $businessdoc_insured->coverage_id = '40';          $businessdoc_insured->premium = '1340340';         $businessdoc_insured->save(); 
        $businessdoc_insured=new businessdoc_insured();          $businessdoc_insured->id = 'b242732e-4e87-4799-a309-be3716050cfd';        $businessdoc_insured->biz_document_id = '2015-ADA005-002-01';         $businessdoc_insured->company_id = '39';         $businessdoc_insured->coverage_id = '40';          $businessdoc_insured->premium = '21826290';         $businessdoc_insured->save(); 
        $businessdoc_insured=new businessdoc_insured();          $businessdoc_insured->id = '2ef8f6c6-6ed6-43cb-b059-728135c2a3c7';        $businessdoc_insured->biz_document_id = '2015-ADA005-003-01';         $businessdoc_insured->company_id = '39';         $businessdoc_insured->coverage_id = '40';          $businessdoc_insured->premium = '2902600.05';         $businessdoc_insured->save(); 
        $businessdoc_insured=new businessdoc_insured();          $businessdoc_insured->id = '170c060d-75b1-4889-89dd-aa64d2d2a2e2';        $businessdoc_insured->biz_document_id = '2016-ADA005-001-01';         $businessdoc_insured->company_id = '39';         $businessdoc_insured->coverage_id = '40';          $businessdoc_insured->premium = '10468198.2';         $businessdoc_insured->save(); 
    }
}
