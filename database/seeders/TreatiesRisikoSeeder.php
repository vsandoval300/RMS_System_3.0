<?php

namespace Database\Seeders;

use App\Models\Treaty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TreatiesRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $Treaty=new Treaty();          $Treaty->treaty_code = 'TTY-2021-RIS029-006';         $Treaty->name = '2021 Property & Power Generation Facultative Agreement';        $Treaty->reinsurer_id = '36';         $Treaty->description = 'This Binding Authority Agreement establishes the terms under which Risiko Reinsurance Ltd. authorizes Dynamic Reinsurance LLC to bind facultative reinsurance for Property and Power Generation risks. It defines the scope of authority, underwriting limits, excluded classes, territorial restrictions, and reporting obligations. The agreement also outlines premium handling, claims authority, compliance requirements, and termination conditions governing the relationship between the parties.';                 $Treaty->contract_type =  'Binder ' ;                 $Treaty->created_at = date('Y-m-d',strtotime('2021-12-31'));         $Treaty->updated_at = date('Y-m-d',strtotime('2021-12-31'));       $Treaty->save();  
    }
}