<?php

namespace Database\Seeders;

use App\Models\cscheme;
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
        $cscheme=new cscheme();          $cscheme->id = 'SCHE-20141226-0103';        $cscheme->index = '94';          $cscheme->share = '1';            $cscheme->agreement_type = 'Quota Share';          $cscheme->save(); 
        $cscheme=new cscheme();          $cscheme->id = 'SCHE-20150909-0104';        $cscheme->index = '95';          $cscheme->share = '1';            $cscheme->agreement_type = 'Quota Share';          $cscheme->save(); 
        $cscheme=new cscheme();          $cscheme->id = 'SCHE-20150909-0105';        $cscheme->index = '96';          $cscheme->share = '1';            $cscheme->agreement_type = 'Quota Share';          $cscheme->save(); 
        $cscheme=new cscheme();          $cscheme->id = 'SCHE-20151224-0106';        $cscheme->index = '97';          $cscheme->share = '1';            $cscheme->agreement_type = 'Quota Share';          $cscheme->save(); 
        $cscheme=new cscheme();          $cscheme->id = 'SCHE-20151224-0107';        $cscheme->index = '98';          $cscheme->share = '1';            $cscheme->agreement_type = 'Quota Share';          $cscheme->save(); 
        $cscheme=new cscheme();          $cscheme->id = 'SCHE-20160330-0108';        $cscheme->index = '99';          $cscheme->share = '1';            $cscheme->agreement_type = 'Quota Share';          $cscheme->save(); 
    }
}
