<?php

namespace Database\Seeders;

use App\Models\CostScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CschemeInvictusSeeder2026 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20260213-0001';        $CostScheme->index = '1';          $CostScheme->share = '0.5';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2026-02-13'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2026-02-13'));      $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20260213-0002';        $CostScheme->index = '2';          $CostScheme->share = '0.5';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2026-02-13'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2026-02-13'));      $CostScheme->save();  

    }
}