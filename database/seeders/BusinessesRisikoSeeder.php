<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Business;

class BusinessesRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $business=new business();           $business->business_code = '2021-RIS0029-003';          $business->index = '1';             $business->description = 'Facultative Reinsurance Contract';         $business->reinsurance_type = 'Facultative';         $business->risk_covered = 'Non-Life';               $business->business_type = 'Third party';                $business->premium_type = 'Fixed';               $business->purpose = 'Traditional';                  $business->claims_type = 'Claims occurrence';              $business->reinsurer_id = '36';                 $business->parent_id = null;             $business->renewed_from_id = null;             $business->producer_id = '3';              $business->currency_id = '109';                $business->region_id = '2';              $business->approval_status = 'APR';            $business->approval_status_updated_at= date('Y-m-d',strtotime('2021-10-26'));           $business->business_lifecycle_status = 'Expired';         $business->business_lifecycle_status_updated_at= date('Y-m-d',strtotime('2021-10-26'));           $business->created_at = date('Y-m-d',strtotime('2021-10-26'));           $business->save(); 
    }
}
