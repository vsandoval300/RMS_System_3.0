<?php

namespace Database\Seeders;

use App\Models\operative_status;
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
        $operative_status = new operative_status();
        $operative_status->acronym = 'PL';
        $operative_status->description = 'Pending license';
        $operative_status->save();

        $operative_status = new operative_status();
        $operative_status->acronym = 'OP';
        $operative_status->description = 'Operative';
        $operative_status->save();

        $operative_status = new operative_status();
        $operative_status->acronym = 'TR';
        $operative_status->description = 'Transferred';
        $operative_status->save();

        $operative_status = new operative_status();
        $operative_status->acronym = 'DV';
        $operative_status->description = 'Dissolved';
        $operative_status->save();

        $operative_status = new operative_status();
        $operative_status->acronym = 'RO';
        $operative_status->description = 'Run-off';
        $operative_status->save();

        $operative_status = new operative_status();
        $operative_status->acronym = 'DS';
        $operative_status->description = 'Dormant';
        $operative_status->save();

        $operative_status = new operative_status();
        $operative_status->acronym = 'PI';
        $operative_status->description = 'Pending incop.';
        $operative_status->save();
    }
}
