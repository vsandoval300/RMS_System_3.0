<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionsTypesSeeder extends Seeder
{
    public function run(): void
    {
        // ‼️  Estos valores DEBEN coincidir con los permitidos en el enum

        $TransactionType = new TransactionType();
        $TransactionType->description ='Premium';
        $TransactionType->save();

        $TransactionType = new TransactionType();
        $TransactionType->description ='Claims';
        $TransactionType->save();

        $TransactionType = new TransactionType();
        $TransactionType->description ='Claims Reserve';
        $TransactionType->save();


    }
}
