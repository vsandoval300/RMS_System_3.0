<?php

namespace Database\Seeders;

use App\Models\deduction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeductionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $deduction = new deduction();
        $deduction->concept = 'fee';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = '';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = 'referral';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = 'tax';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = 'reserve';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = 'retail fee';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = 'other ded';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = 'exempt';
        $deduction->description = '';
        $deduction->save();
    }
}
