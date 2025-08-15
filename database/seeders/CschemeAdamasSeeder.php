<?php

namespace Database\Seeders;

use App\Models\CostScheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CschemeAdamasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20141226-0103';        $CostScheme->index = '94';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20150909-0104';        $CostScheme->index = '95';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20150909-0105';        $CostScheme->index = '96';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20151224-0106';        $CostScheme->index = '97';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20151224-0107';        $CostScheme->index = '98';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
        $CostScheme=new CostScheme();          $CostScheme->id = 'SCHE-20160330-0108';        $CostScheme->index = '99';          $CostScheme->share = '1';            $CostScheme->agreement_type = 'Quota Share';          $CostScheme->save(); 
    }
}
