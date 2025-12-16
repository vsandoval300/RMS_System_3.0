<?php

namespace Database\Seeders;

use App\Models\TreatyDoc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TreatiesDocRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $TreatyDoc=new TreatyDoc();          $TreatyDoc->id = '1';         $TreatyDoc->index = '1';        $TreatyDoc->treaty_code = 'TTY-2021-RIS029-006';             $TreatyDoc->document_path =  'reinsurers/Treaties/TTY-2021-RIS029-006-FDVBBL.pdf ' ;                 $TreatyDoc->created_at = date('Y-m-d',strtotime('2021-12-31'));         $TreatyDoc->updated_at = date('Y-m-d',strtotime('2021-12-31'));        $TreatyDoc->save(); 
        $TreatyDoc=new TreatyDoc();          $TreatyDoc->id = '2';         $TreatyDoc->index = '2';        $TreatyDoc->treaty_code = 'TTY-2021-RIS029-006';             $TreatyDoc->document_path =  'reinsurers/Treaties/TTY-2021-RIS029-006-HGNKJH.pdf ' ;                 $TreatyDoc->created_at = date('Y-m-d',strtotime('2021-12-31'));         $TreatyDoc->updated_at = date('Y-m-d',strtotime('2021-12-31'));        $TreatyDoc->save();  
    }
}
