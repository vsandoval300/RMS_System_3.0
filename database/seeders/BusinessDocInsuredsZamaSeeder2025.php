<?php

namespace Database\Seeders;

use App\Models\BusinessOpDocsInsured;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessDocInsuredsZamaSeeder2025 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();      $BusinessOpDocsInsured->id = '6b40f1b4-7446-4da1-9980-1f72edff99c8';     $BusinessOpDocsInsured->op_document_id = '2025-ZMA056-001-01';     $BusinessOpDocsInsured->cscheme_id = 'SCHE-20250801-0011';       $BusinessOpDocsInsured->company_id = '523';      $BusinessOpDocsInsured->coverage_id = '2';      $BusinessOpDocsInsured->premium = '4639830.50847458';      $BusinessOpDocsInsured->created_at = date('Y-m-d',strtotime('2024-12-01'));          $BusinessOpDocsInsured->updated_at = date('Y-m-d',strtotime('2024-12-01'));    $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();      $BusinessOpDocsInsured->id = '93d16580-d500-4dc6-929c-74915e6175fe';     $BusinessOpDocsInsured->op_document_id = '2025-ZMA056-002-01';     $BusinessOpDocsInsured->cscheme_id = 'SCHE-20251201-0043';       $BusinessOpDocsInsured->company_id = '206';      $BusinessOpDocsInsured->coverage_id = '56';      $BusinessOpDocsInsured->premium = '415916.68';      $BusinessOpDocsInsured->created_at = date('Y-m-d',strtotime('2025-12-31'));          $BusinessOpDocsInsured->updated_at = date('Y-m-d',strtotime('2025-12-31'));    $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();      $BusinessOpDocsInsured->id = 'e539a209-0ddf-43ad-8e31-3e9dfd88acd0';     $BusinessOpDocsInsured->op_document_id = '2025-ZMA056-003-01';     $BusinessOpDocsInsured->cscheme_id = 'SCHE-20251201-0043';       $BusinessOpDocsInsured->company_id = '206';      $BusinessOpDocsInsured->coverage_id = '84';      $BusinessOpDocsInsured->premium = '249999.99';      $BusinessOpDocsInsured->created_at = date('Y-m-d',strtotime('2025-12-31'));          $BusinessOpDocsInsured->updated_at = date('Y-m-d',strtotime('2025-12-31'));    $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();      $BusinessOpDocsInsured->id = '5f315dff-1b41-4720-9f0f-b659c8a7fe52';     $BusinessOpDocsInsured->op_document_id = '2025-ZMA056-004-01';     $BusinessOpDocsInsured->cscheme_id = 'SCHE-20260101-0017';       $BusinessOpDocsInsured->company_id = '522';      $BusinessOpDocsInsured->coverage_id = '26';      $BusinessOpDocsInsured->premium = '551510.99';      $BusinessOpDocsInsured->created_at = date('Y-m-d',strtotime('2026-01-01'));          $BusinessOpDocsInsured->updated_at = date('Y-m-d',strtotime('2026-01-01'));    $BusinessOpDocsInsured->save(); 
        $BusinessOpDocsInsured=new BusinessOpDocsInsured();      $BusinessOpDocsInsured->id = 'c0d6293a-15eb-4467-84fc-8436600e041c';     $BusinessOpDocsInsured->op_document_id = '2025-ZMA056-004-02';     $BusinessOpDocsInsured->cscheme_id = 'SCHE-20260101-0017';       $BusinessOpDocsInsured->company_id = '522';      $BusinessOpDocsInsured->coverage_id = '26';      $BusinessOpDocsInsured->premium = '-551510.99';      $BusinessOpDocsInsured->created_at = date('Y-m-d',strtotime('2026-01-01'));          $BusinessOpDocsInsured->updated_at = date('Y-m-d',strtotime('2026-01-01'));    $BusinessOpDocsInsured->save();  

    }
}