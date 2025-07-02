<?php

namespace Database\Seeders;

use App\Models\OperativeStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $OperativeStatus = new OperativeStatus();
        $OperativeStatus->acronym = 'PL';
        $OperativeStatus->description = 'Pending license';
        $OperativeStatus->save();

        $OperativeStatus = new OperativeStatus();
        $OperativeStatus->acronym = 'OP';
        $OperativeStatus->description = 'Operative';
        $OperativeStatus->save();

        $OperativeStatus = new OperativeStatus();
        $OperativeStatus->acronym = 'TR';
        $OperativeStatus->description = 'Transferred';
        $OperativeStatus->save();

        $OperativeStatus = new OperativeStatus();
        $OperativeStatus->acronym = 'DV';
        $OperativeStatus->description = 'Dissolved';
        $OperativeStatus->save();

        $OperativeStatus = new OperativeStatus();
        $OperativeStatus->acronym = 'RO';
        $OperativeStatus->description = 'Run-off';
        $OperativeStatus->save();

        $OperativeStatus = new OperativeStatus();
        $OperativeStatus->acronym = 'DS';
        $OperativeStatus->description = 'Dormant';
        $OperativeStatus->save();

        $OperativeStatus = new OperativeStatus();
        $OperativeStatus->acronym = 'PI';
        $OperativeStatus->description = 'Pending incop.';
        $OperativeStatus->save();
    }
}
