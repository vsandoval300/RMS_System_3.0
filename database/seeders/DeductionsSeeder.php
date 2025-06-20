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
        $deduction->concept = 'retention';
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
        $deduction->concept = 'retail';
        $deduction->description = '';
        $deduction->save();

        $deduction = new deduction();
        $deduction->concept = 'other deduction';
        $deduction->description = '';
        $deduction->save();
    }
}
