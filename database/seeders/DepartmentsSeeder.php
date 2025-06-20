<?php

namespace Database\Seeders;

use App\Models\department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $department = new department();
        $department->name = 'General Direction';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Underwriting';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Sales';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Operations & Administration';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Captives Administration';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Head Office';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Legal';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Finance';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Information Technology';
        $department->business_unit_id = '1';
        $department->save();

        $department = new department();
        $department->name = 'Family Office';
        $department->business_unit_id = '1';
        $department->save();
    }
}
