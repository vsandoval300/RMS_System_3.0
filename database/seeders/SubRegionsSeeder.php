<?php

namespace Database\Seeders;

use App\Models\subregion;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubRegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $region = new subregion();
        $region->name = 'Northern Africa';
        $region->subregion_code = '15';
        $region->region_id = '1';
        $region->save();

        $region = new subregion();
        $region->name = 'Sub-Saharan Africa';
        $region->subregion_code = '202';
        $region->region_id = '1';
        $region->save();

        $region = new subregion();
        $region->name = 'Latin America and the Caribbean';
        $region->subregion_code = '419';
        $region->region_id = '2';
        $region->save();
        
        $region = new subregion();
        $region->name = 'Northern America';
        $region->subregion_code = '21';
        $region->region_id = '2';
        $region->save();

        $region = new subregion();
        $region->name = 'Central Asia';
        $region->subregion_code = '143';
        $region->region_id = '3';
        $region->save();

        $region = new subregion();
        $region->name = 'Eastern Asia';
        $region->subregion_code = '30';
        $region->region_id = '3';
        $region->save();

        $region = new subregion();
        $region->name = 'South-eastern Asia';
        $region->subregion_code = '35';
        $region->region_id = '3';
        $region->save();

        $region = new subregion();
        $region->name = 'Southern Asia';
        $region->subregion_code = '34';
        $region->region_id = '3';
        $region->save();
        
        $region = new subregion();
        $region->name = 'Western Asia';
        $region->subregion_code = '145';
        $region->region_id = '3';
        $region->save();

        $region = new subregion();
        $region->name = 'Eastern Europe';
        $region->subregion_code = '151';
        $region->region_id = '4';
        $region->save();

        $region = new subregion();
        $region->name = 'Northern Europe';
        $region->subregion_code = '154';
        $region->region_id = '4';
        $region->save();

        $region = new subregion();
        $region->name = 'Southern Europe';
        $region->subregion_code = '39';
        $region->region_id = '4';
        $region->save();

        $region = new subregion();
        $region->name = 'Western Europe';
        $region->subregion_code = '155';
        $region->region_id = '4';
        $region->save();

        $region = new subregion();
        $region->name = 'Australia and New Zealand';
        $region->subregion_code = '53';
        $region->region_id = '5';
        $region->save();

        $region = new subregion();
        $region->name = 'Melanesia';
        $region->subregion_code = '54';
        $region->region_id = '5';
        $region->save();

        $region = new subregion();
        $region->name = 'Micronesia';
        $region->subregion_code = '57';
        $region->region_id = '5';
        $region->save();

        $region = new subregion();
        $region->name = 'Polynesia';
        $region->subregion_code = '61';
        $region->region_id = '5';
        $region->save();

        $region = new subregion();
        $region->name = 'Worldwide';
        $region->subregion_code = '1';
        $region->region_id = '6';
        $region->save();

        $region = new subregion();
        $region->name = 'Antarctica';
        $region->subregion_code = '3';
        $region->region_id = '7';
        $region->save();
    }
}
