<?php

namespace Database\Seeders;

use App\Models\CostNodex;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostNodesxTobahSeeder2026 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20260201-0001-14D6FNenSqUHM7TVEEkp6BvbhB';       $CostNodex->index = '1';        $CostNodex->concept = '8';        $CostNodex->value = '0';         $CostNodex->partner_source_id = '94';        $CostNodex->partner_destination_id =  '59 ' ;              $CostNodex->cscheme_id = 'SCHE-20260201-0001';         $CostNodex->save(); 
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20260201-0001-BZhpSW6SZx7arLNZRTF2NhQrTG';       $CostNodex->index = '2';        $CostNodex->concept = '1';        $CostNodex->value = '0.035';         $CostNodex->partner_source_id = '59';        $CostNodex->partner_destination_id =  '23 ' ;              $CostNodex->cscheme_id = 'SCHE-20260201-0001';         $CostNodex->save(); 
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20260201-0001-7JawfqwdxKnftXYmcfTbdw3hjS';       $CostNodex->index = '3';        $CostNodex->concept = '1';        $CostNodex->value = '0.02';         $CostNodex->partner_source_id = '23';        $CostNodex->partner_destination_id =  '103 ' ;              $CostNodex->cscheme_id = 'SCHE-20260201-0001';         $CostNodex->save();  

    }
}