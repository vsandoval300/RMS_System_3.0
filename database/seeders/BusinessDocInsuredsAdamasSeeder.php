<?php

namespace Database\Seeders;

use App\Models\BusinessOpDocsInsured;
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
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();          $BusinessOpDocsInsured->id = 'f2707322-e188-42cc-bd53-fd2b56d05e3e';        $BusinessOpDocsInsured->op_document_id = '2014-ADA005-001-01';         $BusinessOpDocsInsured->company_id = '39';         $BusinessOpDocsInsured->coverage_id = '40';          $BusinessOpDocsInsured->premium = '4867500.15';         $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();          $BusinessOpDocsInsured->id = '34210fe2-480b-4021-aa7a-780e72258a81';        $BusinessOpDocsInsured->op_document_id = '2014-ADA005-002-01';         $BusinessOpDocsInsured->company_id = '39';         $BusinessOpDocsInsured->coverage_id = '40';          $BusinessOpDocsInsured->premium = '3035381.85';         $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();          $BusinessOpDocsInsured->id = 'fd5a7d17-772b-475e-8c44-07ae14b42f87';        $BusinessOpDocsInsured->op_document_id = '2015-ADA005-001-01';         $BusinessOpDocsInsured->company_id = '39';         $BusinessOpDocsInsured->coverage_id = '40';          $BusinessOpDocsInsured->premium = '1340340';         $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();          $BusinessOpDocsInsured->id = 'b242732e-4e87-4799-a309-be3716050cfd';        $BusinessOpDocsInsured->op_document_id = '2015-ADA005-002-01';         $BusinessOpDocsInsured->company_id = '39';         $BusinessOpDocsInsured->coverage_id = '40';          $BusinessOpDocsInsured->premium = '21826290';         $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();          $BusinessOpDocsInsured->id = '2ef8f6c6-6ed6-43cb-b059-728135c2a3c7';        $BusinessOpDocsInsured->op_document_id = '2015-ADA005-003-01';         $BusinessOpDocsInsured->company_id = '39';         $BusinessOpDocsInsured->coverage_id = '40';          $BusinessOpDocsInsured->premium = '2902600.05';         $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();          $BusinessOpDocsInsured->id = '170c060d-75b1-4889-89dd-aa64d2d2a2e2';        $BusinessOpDocsInsured->op_document_id = '2016-ADA005-001-01';         $BusinessOpDocsInsured->company_id = '39';         $BusinessOpDocsInsured->coverage_id = '40';          $BusinessOpDocsInsured->premium = '10468198.2';         $BusinessOpDocsInsured->save();  
    }
}
