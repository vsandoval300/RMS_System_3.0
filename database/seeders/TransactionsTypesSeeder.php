<?php

namespace Database\Seeders;

use App\Models\transactions_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class TransactionsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        //
        $transactions_type = new transactions_type();
        $transactions_type->description ='Premium';
        $transactions_type->save();

        $transactions_type = new transactions_type();
        $transactions_type->description ='Claims';
        $transactions_type->save();

        $transactions_type = new transactions_type();
        $transactions_type->description ='Claims Reserve';
        $transactions_type->save();
    }
}
