<?php

namespace Database\Seeders;

use App\Models\LiabilityStructure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiabilityStructureLogSeeder2025 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $LiabilityStructure=new LiabilityStructure();        $LiabilityStructure->index =  '1' ;                    $LiabilityStructure->coverage_id =  '9' ;                            $LiabilityStructure->cls =  'TRUE' ;                      $LiabilityStructure->limit =  '5000000' ;                             $LiabilityStructure->limit_desc =  'Each and every loss and in the annual aggregate.' ;                             $LiabilityStructure->sublimit = null;                       $LiabilityStructure->sublimit_desc = null;                       $LiabilityStructure->deductible =  '30' ;                      $LiabilityStructure->deductible_desc =  'Days waiting' ;                        $LiabilityStructure->business_code =  '2025-LOG042-001' ;              $LiabilityStructure->created_at = date('Y-m-d',strtotime('2025-12-31'));   $LiabilityStructure->updated_at = date('Y-m-d',strtotime('2025-12-31'));      $LiabilityStructure->save(); 
        $LiabilityStructure=new LiabilityStructure();        $LiabilityStructure->index =  '1' ;                    $LiabilityStructure->coverage_id =  '41' ;                            $LiabilityStructure->cls =  'TRUE' ;                      $LiabilityStructure->limit =  '5000000' ;                             $LiabilityStructure->limit_desc =  'Each and every loss and in the annual aggregate.' ;                             $LiabilityStructure->sublimit =  '1400000' ;                        $LiabilityStructure->sublimit_desc =  'Per event and in the annual aggregate.' ;                        $LiabilityStructure->deductible =  '3' ;                      $LiabilityStructure->deductible_desc =  'Days waiting' ;                        $LiabilityStructure->business_code =  '2025-LOG042-001' ;              $LiabilityStructure->created_at = date('Y-m-d',strtotime('2025-12-31'));   $LiabilityStructure->updated_at = date('Y-m-d',strtotime('2025-12-31'));      $LiabilityStructure->save(); 
        $LiabilityStructure=new LiabilityStructure();        $LiabilityStructure->index =  '1' ;                    $LiabilityStructure->coverage_id =  '56' ;                            $LiabilityStructure->cls =  'FALSE' ;                      $LiabilityStructure->limit =  '17354400' ;                             $LiabilityStructure->limit_desc =  'Maximum Sum Insured for all persons' ;                             $LiabilityStructure->sublimit = null;                       $LiabilityStructure->sublimit_desc = null;                       $LiabilityStructure->deductible = null;                     $LiabilityStructure->deductible_desc = null;                       $LiabilityStructure->business_code =  '2025-LOG042-002' ;              $LiabilityStructure->created_at = date('Y-m-d',strtotime('2025-12-31'));   $LiabilityStructure->updated_at = date('Y-m-d',strtotime('2025-12-31'));      $LiabilityStructure->save(); 
        $LiabilityStructure=new LiabilityStructure();        $LiabilityStructure->index =  '1' ;                    $LiabilityStructure->coverage_id =  '43' ;                            $LiabilityStructure->cls =  'FALSE' ;                      $LiabilityStructure->limit =  '13200000' ;                             $LiabilityStructure->limit_desc =  'For this bond.' ;                             $LiabilityStructure->sublimit = null;                       $LiabilityStructure->sublimit_desc = null;                       $LiabilityStructure->deductible = null;                     $LiabilityStructure->deductible_desc = null;                       $LiabilityStructure->business_code =  '2025-LOG042-003' ;              $LiabilityStructure->created_at = date('Y-m-d',strtotime('2025-12-31'));   $LiabilityStructure->updated_at = date('Y-m-d',strtotime('2025-12-31'));      $LiabilityStructure->save(); 

    }
}