<?php

namespace Database\Seeders;

use App\Models\CostNodex;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostNodesxRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20211231-0101-1';       $CostNodex->index = '1';        $CostNodex->concept = '4';        $CostNodex->value = '0.02';         $CostNodex->partner_source_id = '65';        $CostNodex->partner_destination_id =  '13 ' ;              $CostNodex->cscheme_id = 'SCHE-20211231-0101';         $CostNodex->save(); 
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20211231-0101-2';       $CostNodex->index = '2';        $CostNodex->concept = '1';        $CostNodex->value = '0.03';         $CostNodex->partner_source_id = '13';        $CostNodex->partner_destination_id =  '103 ' ;              $CostNodex->cscheme_id = 'SCHE-20211231-0101';         $CostNodex->save(); 
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20211231-0102-1';       $CostNodex->index = '1';        $CostNodex->concept = '4';        $CostNodex->value = '0.02';         $CostNodex->partner_source_id = '65';        $CostNodex->partner_destination_id =  '13 ' ;              $CostNodex->cscheme_id = 'SCHE-20211231-0102';         $CostNodex->save(); 
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20211231-0102-2';       $CostNodex->index = '2';        $CostNodex->concept = '1';        $CostNodex->value = '0.03';         $CostNodex->partner_source_id = '13';        $CostNodex->partner_destination_id =  '103 ' ;              $CostNodex->cscheme_id = 'SCHE-20211231-0102';         $CostNodex->save(); 
    }
}
