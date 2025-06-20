<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $region = new Region();
        $region->name = 'Africa';
        $region->region_code = '2';
        $region->save();

        $region = new Region();
        $region->name = 'America';
        $region->region_code = '19';
        $region->save();

        $region = new Region();
        $region->name = 'Asia';
        $region->region_code = '142';
        $region->save();

        $region = new Region();
        $region->name = 'Europe';
        $region->region_code = '150';
        $region->save();

        $region = new Region();
        $region->name = 'Oceania';
        $region->region_code = '9';
        $region->save();

        $region = new Region();
        $region->name = 'Worlwide';
        $region->region_code = '1';
        $region->save();

        $region = new Region();
        $region->name = 'Antarctica';
        $region->region_code = '3';
        $region->save();
    }
}
