<?php

namespace Database\Seeders;

use App\Models\ReinsurerType;
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
        $ReinsurerType = new ReinsurerType();
        $ReinsurerType->type_acronym ='CC';
        $ReinsurerType->description = 'Captive Cell';
        $ReinsurerType->save();

        $ReinsurerType = new ReinsurerType();
        $ReinsurerType->type_acronym ='SA';
        $ReinsurerType->description = 'Stand Alone';
        $ReinsurerType->save();

        $ReinsurerType = new ReinsurerType();
        $ReinsurerType->type_acronym ='RC';
        $ReinsurerType->description = 'Reinsurance Captive';
        $ReinsurerType->save();
    }
}
