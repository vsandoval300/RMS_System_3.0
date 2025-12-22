<?php

namespace Database\Seeders;

use App\Models\CostScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CschemeRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20211231-0101';        $CostScheme->index = '1';          $CostScheme->share = '0.2';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'MXN 3,000,000 each and every loss and in the annual aggregate';         $CostScheme->created_at = date('Y-m-d',strtotime('2021-10-26'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2021-10-26'));      $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20211231-0102';        $CostScheme->index = '2';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';         $CostScheme->description = 'MXN 50,000 each and every loss capped at MXN 500,000 in the annual aggregate.';         $CostScheme->created_at = date('Y-m-d',strtotime('2021-10-26'));         $CostScheme->updated_at = date('Y-m-d',strtotime('2021-10-26'));      $CostScheme->save(); 
    }
}
