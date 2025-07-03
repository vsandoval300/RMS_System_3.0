<?php

namespace Database\Seeders;

use App\Models\CostsScheme;
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
        $CostsScheme=new CostsScheme();          $CostsScheme->id = 'SCHE-20141226-0103';        $CostsScheme->index = '94';          $CostsScheme->share = '1';            $CostsScheme->agreement_type = 'Quota Share';          $CostsScheme->save(); 
        $CostsScheme=new CostsScheme();          $CostsScheme->id = 'SCHE-20150909-0104';        $CostsScheme->index = '95';          $CostsScheme->share = '1';            $CostsScheme->agreement_type = 'Quota Share';          $CostsScheme->save(); 
        $CostsScheme=new CostsScheme();          $CostsScheme->id = 'SCHE-20150909-0105';        $CostsScheme->index = '96';          $CostsScheme->share = '1';            $CostsScheme->agreement_type = 'Quota Share';          $CostsScheme->save(); 
        $CostsScheme=new CostsScheme();          $CostsScheme->id = 'SCHE-20151224-0106';        $CostsScheme->index = '97';          $CostsScheme->share = '1';            $CostsScheme->agreement_type = 'Quota Share';          $CostsScheme->save(); 
        $CostsScheme=new CostsScheme();          $CostsScheme->id = 'SCHE-20151224-0107';        $CostsScheme->index = '98';          $CostsScheme->share = '1';            $CostsScheme->agreement_type = 'Quota Share';          $CostsScheme->save(); 
        $CostsScheme=new CostsScheme();          $CostsScheme->id = 'SCHE-20160330-0108';        $CostsScheme->index = '99';          $CostsScheme->share = '1';            $CostsScheme->agreement_type = 'Quota Share';          $CostsScheme->save(); 
    }
}
