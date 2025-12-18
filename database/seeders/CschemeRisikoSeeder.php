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
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20211231-0101';        $CostScheme->index = '1';          $CostScheme->share = '0.2';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20211231-0102';        $CostScheme->index = '2';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
    }
}
