<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
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
        BusinessUnit::create([
            'name'        => 'Alternative Risk Transfer Unit',
            'description' => 'None',
            'client_id'   => 1,
        ]);
    }
}
