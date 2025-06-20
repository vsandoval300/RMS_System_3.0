<?php

namespace Database\Seeders;

use App\Models\business_unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $business_unit = new business_unit();
        $business_unit->name = 'Alternative Risk Transfer Unit';
        $business_unit->description = 'None';
        $business_unit->client_id = '1';
        $business_unit->save();
    }
}
