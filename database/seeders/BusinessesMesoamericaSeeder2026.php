<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessesMesoamericaSeeder2026 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $business=new business();           $business->business_code = '2026-MES022-001';          $business->index = '1';             $business->description = 'Facultative Reinsurance for General Third-Party Liability';         $business->reinsurance_type = 'Facultative';         $business->risk_covered = 'Non-Life';               $business->business_type = 'Own';                $business->premium_type = 'Fixed';               $business->purpose = 'Strategic';                  $business->claims_type = 'Claims occurrence';              $business->reinsurer_id = '26';                 $business->parent_id = null;             $business->renewed_from_id = null;             $business->producer_id = '96';              $business->currency_id = '157';                $business->region_id = '2';              $business->approval_status = 'APR';            $business->approval_status_updated_at= date('Y-m-d',strtotime('2026-02-01'));           $business->business_lifecycle_status = 'In Force';         $business->business_lifecycle_status_updated_at= date('Y-m-d',strtotime('2026-02-01'));            $business->source_code = null;             $business->created_by_user =  '14 ' ;              $business->created_at = date('Y-m-d',strtotime('2026-02-01'));           $business->save();  

    }
}