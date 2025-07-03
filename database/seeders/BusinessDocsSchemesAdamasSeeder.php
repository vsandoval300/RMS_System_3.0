<?php

namespace Database\Seeders;

use App\Models\BusinessOpDocsScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessDocsSchemesAdamasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = '515f6820-4952-453a-aba2-871261aea11c';        $BusinessOpDocsScheme->index = '1';         $BusinessOpDocsScheme->business_doc_id = '2014-ADA005-001-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20141226-0103';       $BusinessOpDocsScheme->save(); 
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = 'a76f45cf-6128-476f-bad0-baa3e5cc47ca';        $BusinessOpDocsScheme->index = '1';         $BusinessOpDocsScheme->business_doc_id = '2014-ADA005-002-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20150909-0104';       $BusinessOpDocsScheme->save(); 
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = '997722ab-bed7-4ade-8bfe-2d10e895c27c';        $BusinessOpDocsScheme->index = '1';         $BusinessOpDocsScheme->business_doc_id = '2015-ADA005-001-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20150909-0105';       $BusinessOpDocsScheme->save(); 
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = 'd17b8b88-1397-4f3c-9757-cf51b7ebd476';        $BusinessOpDocsScheme->index = '1';         $BusinessOpDocsScheme->business_doc_id = '2015-ADA005-002-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20151224-0106';       $BusinessOpDocsScheme->save(); 
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = '980dfa83-fb75-466a-b4f9-554f3e9b8ea2';        $BusinessOpDocsScheme->index = '1';         $BusinessOpDocsScheme->business_doc_id = '2015-ADA005-003-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20151224-0107';       $BusinessOpDocsScheme->save(); 
        $BusinessOpDocsScheme=new BusinessOpDocsScheme();          $BusinessOpDocsScheme->id = 'ce051a01-f77f-494a-aa06-1aec04c70287';        $BusinessOpDocsScheme->index = '1';         $BusinessOpDocsScheme->business_doc_id = '2016-ADA005-001-01';         $BusinessOpDocsScheme->cscheme_id = 'SCHE-20160330-0108';       $BusinessOpDocsScheme->save(); 
    }
}
