<?php

namespace Database\Seeders;

use App\Models\other_transaction_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OtherTransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'StC';
        $other_transaction_type->description = 'Access Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'LiF';
        $other_transaction_type->description = 'Management Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'AuF';
        $other_transaction_type->description = 'Retail Refererral Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'LeF';
        $other_transaction_type->description = 'Broker Refererral Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'AcF';
        $other_transaction_type->description = 'Reinsurer Refererral Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'DiF';
        $other_transaction_type->description = 'Reinsurer Refererral Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'Tax';
        $other_transaction_type->description = 'Reinsurer Refererral Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'Spls';
        $other_transaction_type->description = 'Surplus';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'MeE';
        $other_transaction_type->description = 'Meeting Expenses';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'AnC';
        $other_transaction_type->description = 'Annual Commission';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'TrS';
        $other_transaction_type->description = 'Translation Services';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'FcP';
        $other_transaction_type->description = 'Foreing Currency Permit';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'ScF';
        $other_transaction_type->description = 'Secretarial Fees';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'DyF';
        $other_transaction_type->description = 'Dormancy Yearly Fee';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'SoL';
        $other_transaction_type->description = 'Surrender of License';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'InC';
        $other_transaction_type->description = 'Incorporation Fees';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'CoD';
        $other_transaction_type->description = 'Courier Documents';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'OpC';
        $other_transaction_type->description = 'Operating Costs';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'ClC';
        $other_transaction_type->description = 'Closure Cost';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'EOC';
        $other_transaction_type->description = 'Enginering Operating Costs';
        $other_transaction_type->save();

        $other_transaction_type = new other_transaction_type();
        $other_transaction_type->type = 'ScFM';
        $other_transaction_type->description = 'Multiannual Secretarial Fees';
        $other_transaction_type->save();
    }
}
