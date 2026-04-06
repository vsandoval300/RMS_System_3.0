<?php

namespace Database\Seeders;

use App\Models\CostScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CschemeZamaSeeder2025 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20250801-0011';        $CostScheme->index = '1';          $CostScheme->share = '0.5';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2025-08-01'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2025-08-01'));     $CostScheme->created_by_user = '16';      $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20251201-0043';        $CostScheme->index = '1';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2025-12-01'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2025-12-01'));     $CostScheme->created_by_user = '16';      $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20260101-0017';        $CostScheme->index = '1';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'Each and every loss, subject to the applicable annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2026-01-01'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2026-01-01'));     $CostScheme->created_by_user = '16';      $CostScheme->save();  

    }
}