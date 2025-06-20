<?php

namespace Database\Seeders;

use App\Models\referral;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralsCellMayabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $referral=new referral();          $referral->id = 'e45e5cb7-7ff5-4f0d-b1a2-b7c550bbf747';        $referral->index = '1';         $referral->referral = '0.005';         $referral->costnode_id = 'f93e4b43-efaa-462b-99b2-659550b5962d';         $referral->save(); 
        $referral=new referral();          $referral->id = '5061df3b-878d-4309-a811-abb5dd0e3130';        $referral->index = '1';         $referral->referral = '0.0124';         $referral->costnode_id = '19851749-1e4b-4e55-ac9a-a928a0c0c6cc';         $referral->save(); 
        $referral=new referral();          $referral->id = 'bf4750e4-b0ab-4eec-af31-22f3ecd1538f';        $referral->index = '1';         $referral->referral = '0.0124';         $referral->costnode_id = '243b105d-8c3e-4b13-8dce-160e1caf66e0';         $referral->save(); 
        $referral=new referral();          $referral->id = 'dda923c7-4907-4d0b-9e06-bff69ab87cb8';        $referral->index = '1';         $referral->referral = '0.0125';         $referral->costnode_id = '984f73bb-4198-43e6-80ec-6e5a1652c08f';         $referral->save(); 

    }
}
