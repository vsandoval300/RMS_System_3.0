<?php

namespace Database\Seeders;

use App\Models\CostScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CschemeKoperSeeder2025 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20251231-0001';        $CostScheme->index = '1';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2025-12-01'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2025-12-01'));     $CostScheme->created_by_user = '19';      $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20251231-0002';        $CostScheme->index = '2';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2025-12-01'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2025-12-01'));     $CostScheme->created_by_user = '19';      $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20251231-0003';        $CostScheme->index = '3';          $CostScheme->share = '0.995';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2025-12-01'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2025-12-01'));     $CostScheme->created_by_user = '19';      $CostScheme->save();  

    }
}