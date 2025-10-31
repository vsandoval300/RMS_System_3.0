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
        $manager->address = 'Altman Annex, Derricks, St. James, BB24008, Barbados.';
        $manager->country_id = '20';
        $manager->save();

        $manager = new manager();
        $manager->name = 'Strategic Risk Solutions, Inc.';
        $manager->address = '878 West Bay Road, 2nd Floor North Building, Caribbean Plaza, P.O. Box 1159, Grand Cayman KY1-1102, Cayman Islands.';
        $manager->country_id = '42';
        $manager->save();
    }
}
