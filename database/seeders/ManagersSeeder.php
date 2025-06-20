<?php

namespace Database\Seeders;

use App\Models\manager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $manager = new manager();
        $manager->name = 'Integrity Managers';
        $manager->save();

        $manager = new manager();
        $manager->name = 'Strategic Risk Solutions, Inc.';
        $manager->save();
    }
}
