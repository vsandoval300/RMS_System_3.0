<?php

namespace Database\Seeders;

use App\Models\reinsurer_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReinsurerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $reinsurer_type = new reinsurer_type();
        $reinsurer_type->type_acronym ='CC';
        $reinsurer_type->description = 'Captive Cell';
        $reinsurer_type->save();

        $reinsurer_type = new reinsurer_type();
        $reinsurer_type->type_acronym ='SA';
        $reinsurer_type->description = 'Stand Alone';
        $reinsurer_type->save();

        $reinsurer_type = new reinsurer_type();
        $reinsurer_type->type_acronym ='RC';
        $reinsurer_type->description = 'Reinsurance Captive';
        $reinsurer_type->save();
    }
}
