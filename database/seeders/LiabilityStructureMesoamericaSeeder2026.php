<?php

namespace Database\Seeders;

use App\Models\LiabilityStructure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiabilityStructureMesoamericaSeeder2026 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $LiabilityStructure=new LiabilityStructure();        $LiabilityStructure->index =  '1' ;                    $LiabilityStructure->coverage_id =  '81' ;                            $LiabilityStructure->cls =  '1' ;                      $LiabilityStructure->limit =  '5000000' ;                             $LiabilityStructure->limit_desc =  'Per event and in the annual aggregate, as a single and combined limit in excess of the original deductible for all countries.' ;                             $LiabilityStructure->sublimit = null;                       $LiabilityStructure->sublimit_desc = null;                       $LiabilityStructure->deductible = null;                     $LiabilityStructure->deductible_desc =  'General: USD 2,500 each and every loss; Products Liability: USD 6,000 each and every loss; Export of Products: USD 7,000 each and every loss; US and Canadian Claims: USD 20,000 each and every loss; and Employer’s Liability: USD 5,000 each and every loss, except for claims arising from Chile, which will have a deductible of USD 50,000 each and every loss.' ;                        $LiabilityStructure->business_code =  '2026-MES022-001' ;                         $LiabilityStructure->save();  

    }
}