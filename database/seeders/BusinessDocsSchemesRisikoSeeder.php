<?php

namespace Database\Seeders;

use App\Models\BusinessOpDocsScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessDocsSchemesRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = '32cf40d5-441f-43ed-bb76-9ac36b4d72cb';        $BusinessOpDocsScheme->index = '1';         $BusinessOpDocsScheme->op_document_id = '2021-RIS0029-003-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20211231-0101';       $BusinessOpDocsScheme->save(); 
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = 'f8a16bba-c753-4820-9366-2accd3d6d7c6';        $BusinessOpDocsScheme->index = '2';         $BusinessOpDocsScheme->op_document_id = '2021-RIS0029-003-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20211231-0102';       $BusinessOpDocsScheme->save(); 
    }
}
