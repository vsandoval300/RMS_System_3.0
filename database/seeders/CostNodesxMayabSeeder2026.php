<?php

namespace Database\Seeders;

use App\Models\CostNodex;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostNodesxMayabSeeder2026 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20260215-0001-VN3DUeEY9R0ckzNjCaVdAoekvw';       $CostNodex->index = '1';        $CostNodex->concept = '8';        $CostNodex->value = '0';         $CostNodex->partner_source_id = '94';        $CostNodex->partner_destination_id =  '30 ' ;              $CostNodex->cscheme_id = 'SCHE-20260215-0001';         $CostNodex->save(); 
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20260215-0001-doSInr75gHqOSfSqVJ1XRBvGm1';       $CostNodex->index = '2';        $CostNodex->concept = '1';        $CostNodex->value = '0.035';         $CostNodex->partner_source_id = '30';        $CostNodex->partner_destination_id =  '23 ' ;              $CostNodex->cscheme_id = 'SCHE-20260215-0001';         $CostNodex->save(); 
        $CostNodex=new CostNodex();          $CostNodex->id = 'SCHE-20260215-0001-4RccTWjDFnglIpTuUUkEwd1ofJ';       $CostNodex->index = '3';        $CostNodex->concept = '1';        $CostNodex->value = '0.02';         $CostNodex->partner_source_id = '23';        $CostNodex->partner_destination_id =  '103 ' ;              $CostNodex->cscheme_id = 'SCHE-20260215-0001';         $CostNodex->save(); 

    }
}