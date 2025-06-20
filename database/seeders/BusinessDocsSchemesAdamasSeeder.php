<?php

namespace Database\Seeders;

use App\Models\businessdoc_scheme;
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
        $businessdoc_scheme=new businessdoc_scheme();          $businessdoc_scheme->id = '515f6820-4952-453a-aba2-871261aea11c';        $businessdoc_scheme->index = '1';         $businessdoc_scheme->business_doc_id = '2014-ADA005-001-01';         $businessdoc_scheme->cscheme_id = 'SCHE-20141226-0103';       $businessdoc_scheme->save(); 
        $businessdoc_scheme=new businessdoc_scheme();          $businessdoc_scheme->id = 'a76f45cf-6128-476f-bad0-baa3e5cc47ca';        $businessdoc_scheme->index = '1';         $businessdoc_scheme->business_doc_id = '2014-ADA005-002-01';         $businessdoc_scheme->cscheme_id = 'SCHE-20150909-0104';       $businessdoc_scheme->save(); 
        $businessdoc_scheme=new businessdoc_scheme();          $businessdoc_scheme->id = '997722ab-bed7-4ade-8bfe-2d10e895c27c';        $businessdoc_scheme->index = '1';         $businessdoc_scheme->business_doc_id = '2015-ADA005-001-01';         $businessdoc_scheme->cscheme_id = 'SCHE-20150909-0105';       $businessdoc_scheme->save(); 
        $businessdoc_scheme=new businessdoc_scheme();          $businessdoc_scheme->id = 'd17b8b88-1397-4f3c-9757-cf51b7ebd476';        $businessdoc_scheme->index = '1';         $businessdoc_scheme->business_doc_id = '2015-ADA005-002-01';         $businessdoc_scheme->cscheme_id = 'SCHE-20151224-0106';       $businessdoc_scheme->save(); 
        $businessdoc_scheme=new businessdoc_scheme();          $businessdoc_scheme->id = '980dfa83-fb75-466a-b4f9-554f3e9b8ea2';        $businessdoc_scheme->index = '1';         $businessdoc_scheme->business_doc_id = '2015-ADA005-003-01';         $businessdoc_scheme->cscheme_id = 'SCHE-20151224-0107';       $businessdoc_scheme->save(); 
        $businessdoc_scheme=new businessdoc_scheme();          $businessdoc_scheme->id = 'ce051a01-f77f-494a-aa06-1aec04c70287';        $businessdoc_scheme->index = '1';         $businessdoc_scheme->business_doc_id = '2016-ADA005-001-01';         $businessdoc_scheme->cscheme_id = 'SCHE-20160330-0108';       $businessdoc_scheme->save(); 
    }
}
